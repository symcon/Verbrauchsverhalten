<?php

declare(strict_types=1);

    define('LVL_DAY', 1);
    define('LVL_WEEK', 2);
    define('LVL_MONTH', 3);
    define('LVL_YEAR', 4);

    class Verbrauchsverhalten extends IPSModule
    {
        public function Create()
        {
            //Diese Zeile nicht löschen.
            parent::Create();

            //Properties
            $this->RegisterPropertyInteger('Period', 1);
            $this->RegisterPropertyInteger('Limit', 0);
            $this->RegisterPropertyInteger('OutsideTemperatureID', 0);
            $this->RegisterPropertyInteger('CounterID', 0);

            //Timer
            $this->RegisterPropertyInteger('Interval', 5);
            $this->RegisterTimer('UpdateCalculation', 0, 'VBV_UpdateCalculation($_IPS[\'TARGET\']);');
        }

        public function ApplyChanges()
        {
            //Diese Zeile nicht löschen
            parent::ApplyChanges();

            //Change Profile
            if ($this->ReadPropertyInteger('CounterID') != 0) {
                $id = $this->ReadPropertyInteger('CounterID');

                if (IPS_GetVariable($id)['VariableCustomProfile'] == '') {
                    $profile = IPS_GetVariable($id)['VariableProfile'];
                } else {
                    $profile = IPS_GetVariable($id)['VariableCustomProfile'];
                }
            } else {
                $profile = '';
            }

            //Variables
            $this->RegisterVariableFloat('CurrentValue', $this->Translate('Value of the current Period'), $profile, 10);
            $this->RegisterVariableFloat('CurrentForecast', $this->Translate('Forecast of the current Period'), $profile, 11);
            $this->RegisterVariableFloat('CurrentPrediction', $this->Translate('Prediction of the current Period'), $profile, 12);
            $this->RegisterVariableFloat('CurrentPercent', $this->Translate('Percent of the current Period'), '~Valve.F', 13);
            $this->RegisterVariableFloat('CurrentCoD', $this->Translate('Coefficient of Determination of the current Period'), '', 14);
            $this->RegisterVariableFloat('LastValue', $this->Translate('Value of the last Period'), $profile, 20);
            $this->RegisterVariableFloat('LastForecast', $this->Translate('Forecast of the last Period'), $profile, 21);
            $this->RegisterVariableFloat('LastPrediction', $this->Translate('Prediction of the last Period'), $profile, 22);
            $this->RegisterVariableFloat('LastPercent', $this->Translate('Percent of the last Period'), '~Valve.F', 23);
            $this->RegisterVariableFloat('LastCoD', $this->Translate('Coefficient of Determination of the last Period'), '', 24);

            $this->SetTimerInterval('UpdateCalculation', $this->ReadPropertyInteger('Interval') * 1000 * 60);

            $this->UpdateCalculation();
        }

        public function UpdateCalculation()
        {
            $outsideID = $this->ReadPropertyInteger('OutsideTemperatureID');
            $counterID = $this->ReadPropertyInteger('CounterID');

            if ($outsideID == 0 || $counterID == 0) {
                //No vars selected
                $this->SetStatus(202);
                return;
            } else {
                $this->SetStatus(102);
            }

            $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];

            $aggregationLevel = $this->ReadPropertyInteger('Period');
            switch ($aggregationLevel) {
                case LVL_DAY:
                    $startTimeThisPeriod = strtotime('today 00:00:00', time());
                    $startTimeLastPeriod = strtotime('-1 day', $startTimeThisPeriod);
                    $endTimeThisPeriod = strtotime('+1 day', $startTimeThisPeriod);
                    break;

                case LVL_WEEK:
                    $startTimeThisPeriod = strtotime('monday this week 00:00:00', time());
                    $startTimeLastPeriod = strtotime('-1 week', $startTimeThisPeriod);
                    $endTimeThisPeriod = strtotime('+1 week', $startTimeThisPeriod);
                    break;

                case LVL_MONTH:
                    $startTimeThisPeriod = strtotime('first day of this month 00:00:00', time());
                    $startTimeLastPeriod = strtotime('-1 month', $startTimeThisPeriod);
                    $endTimeThisPeriod = strtotime('+1 month', $startTimeThisPeriod);
                    break;

                case LVL_YEAR:
                    $startTimeThisPeriod = strtotime('first day of january 00:00:00', time());
                    $startTimeLastPeriod = strtotime('-1 year', $startTimeThisPeriod);
                    $endTimeThisPeriod = strtotime('+1 year', $startTimeThisPeriod);
                    break;

                default:
                    $this->SetStatus(200);
                    return;
            }

            //Current period
            $list = $this->calculate($aggregationLevel, $outsideID, $counterID, $startTimeThisPeriod, $endTimeThisPeriod);

            if ($list == []) {
                //status is set from calculate
                return;
            }
            $this->SetValue('CurrentPrediction', $list['prediction']);
            $this->SetValue('CurrentForecast', $list['forecast']);
            $this->SetValue('CurrentPercent', $list['percent']);
            $this->SetValue('CurrentValue', $list['current']);
            $this->SetValue('CurrentCoD', $list['coefficientOfDetermination']);

            //Last period
            $list = $this->calculate($aggregationLevel, $outsideID, $counterID, $startTimeLastPeriod, $startTimeThisPeriod);

            if ($list == []) {
                //status is set from calculate
                return;
            }
            $this->SetValue('LastPrediction', $list['prediction']);
            $this->SetValue('LastForecast', $list['forecast']);
            $this->SetValue('LastPercent', $list['percent']);
            $this->SetValue('LastValue', $list['current']);
            $this->SetValue('LastCoD', $list['coefficientOfDetermination']);
        }

        private function calculate(int $aggregationLevel, int $outsideID, int $counterID, int $startTimePeriod, int $endTimePeriod)
        {
            $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
            $limit = $this->ReadPropertyInteger('Limit');

            //Get xValues
            $rawOutside = AC_GetAggregatedValues($archiveID, $outsideID, $aggregationLevel, 0, $startTimePeriod, $limit);
            $outsideVarValues = [];
            foreach ($rawOutside as $dataset) {
                $outsideVarValues[] = $dataset['Avg'];
            }

            //Get yValues
            $rawCounter = AC_GetAggregatedValues($archiveID, $counterID, $aggregationLevel, 0, $startTimePeriod, $limit);
            $counterVarValues = [];
            foreach ($rawCounter as $dataset) {
                $counterVarValues[] = $dataset['Avg'];
            }

            //Cut the Arrays for equal amount
            if (count($counterVarValues) > count($outsideVarValues)) {
                $counterVarValues = array_slice($counterVarValues, 0, count($outsideVarValues));
            } else {
                $outsideVarValues = array_slice($outsideVarValues, 0, count($counterVarValues));
            }

            //Reverse Arrays for regression
            $valuesCounter = array_reverse($counterVarValues);
            $valuesOutside = array_reverse($outsideVarValues);

            //Valid the values
            if (count($valuesCounter) <= 1) {
                $this->SetStatus(201);
                //The count of values is zero or one which leads to an error in the linear regression
                return [];
            }

            //X = Outside,  Y = Counter
            $parameter = $this->linearRegression($valuesOutside, $valuesCounter);

            //Predict the Consumption
            $currentOutside = AC_GetAggregatedValues($archiveID, $outsideID, $aggregationLevel, $startTimePeriod, $endTimePeriod - 1, 1)[0];
            $predictionPeriod = $parameter['m'] * $currentOutside['Avg'] + $parameter['b'];

            $currentCounter = AC_GetAggregatedValues($archiveID, $counterID, $aggregationLevel, $startTimePeriod, $endTimePeriod - 1, 1)[0];
            $forecastPeriod = $currentCounter['Avg'] / $currentCounter['Duration'] * ($endTimePeriod - $startTimePeriod);

            $percent = ($forecastPeriod / $predictionPeriod) * 100;

            //Print some debug values
            $this->SendDebug('Values', count($valuesOutside), 0);
            $this->SendDebug('OutsideTemperature', $currentOutside['Avg'], 0);
            $this->SendDebug('B', $parameter['b'], 0);
            $this->SendDebug('M', $parameter['m'], 0);
            $this->SendDebug('CoD', $parameter['coefficientOfDetermination'], 0);
            $this->SendDebug('Counter', $currentCounter['Avg'], 0);
            $this->SendDebug('Forecast', $forecastPeriod, 0);

            return [
                'prediction'                 => $predictionPeriod,
                'forecast'                   => $forecastPeriod,
                'percent'                    => $percent,
                'current'                    => $currentCounter['Avg'],
                'coefficientOfDetermination' => $parameter['coefficientOfDetermination'],
            ];
        }

        private function linearRegression(array $valuesX, array $valuesY)
        {
            //Reference https://de.wikipedia.org/wiki/Lineare_Einfachregression
            $averageX = array_sum($valuesX) / count($valuesX);
            $averageY = array_sum($valuesY) / count($valuesY);
            $beta1Denominator = 0;
            $beta1Divider = 0;
            for ($i = 0; $i < count($valuesX); $i++) {
                $beta1Denominator += ($valuesX[$i] - $averageX) * ($valuesY[$i] - $averageY);
                $beta1Divider += pow(($valuesX[$i] - $averageX), 2);
            }
            $beta1 = $beta1Denominator / $beta1Divider;

            $beta0 = $averageY - ($beta1 * $averageX);

            $sqr = 0;
            $sqt = 0;
            for ($i = 0; $i < count($valuesX); $i++) {
                $sqr += pow(($valuesY[$i] - $beta0 - ($beta1 * $valuesX[$i])), 2);
                $sqt += pow(($valuesY[$i] - $averageY), 2);
            }
            $coefficientOfDetermination = 1 - ($sqr / $sqt);
            return [
                'b'                          => $beta0,
                'm'                          => $beta1,
                'coefficientOfDetermination' => $coefficientOfDetermination,
            ];
        }
    }
