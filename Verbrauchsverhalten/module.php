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
            // Diese Zeile nicht löschen.
            parent::Create();

            //Properties
            $this->RegisterPropertyInteger('AggregationLevel', 1);
            $this->RegisterPropertyInteger('Limit', 0);
            //outside temperature is the x
            $this->RegisterPropertyInteger('OutsideTemperatureID', 0);
            //coutner is the y
            $this->RegisterPropertyInteger('CounterID', 0);

            //variables
            $this->RegisterVariableFloat('CurrentPeriod', $this->Translate('Prediction of the current Period'), '', 0);
            $this->RegisterVariableFloat('CurrentValue', $this->Translate('Value of the current Period'), '', 0);
            $this->RegisterVariableFloat('LastPeriod', $this->Translate('Prediction of the last Period'), '', 1);
            $this->RegisterVariableFloat('LastValue', $this->Translate('Value of the last Period'), '', 1);
            $this->RegisterVariableFloat('CurrentPercent', $this->Translate('Percent of the current Period'), '', 0);
            $this->RegisterVariableFloat('LastPercent', $this->Translate('Percent of the last Period'), '', 1);

            //Timer
            $this->RegisterPropertyInteger('Interval', 5);
            $this->RegisterTimer('UpdateCalculationVBV', 0, 'VBV_setData($_IPS[\'TARGET\']);');
        }

        public function ApplyChanges()
        {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            //Change Profile
            $id = $this->ReadPropertyInteger('CounterID');
            if ($id != 0) {
                if (IPS_GetVariable($id)['VariableCustomProfile'] == '') {
                    $profile = IPS_GetVariable($id)['VariableProfile'];
                } else {
                    $profile = IPS_GetVariable($id)['VariableCustomProfile'];
                }
                $this->MaintainVariable('CurrentPeriod', $this->Translate('Prediction of the current Period'), 2, $profile, 0, true);
                $this->MaintainVariable('CurrentValue', $this->Translate('Value of the current Period'), 2, $profile, 0, true);
                $this->MaintainVariable('CurrentPercent', $this->Translate('Percent of the current Period'), 2, $profile, 0, true);
                $this->MaintainVariable('LastPeriod', $this->Translate('Prediction of the last Period'), 2, $profile, 1, true);
                $this->MaintainVariable('LastValue', $this->Translate('Value of the last Period'), 2, $profile, 1, true);
                $this->MaintainVariable('LastPercent', $this->Translate('Percent of the last Period'), 2, $profile, 1, true);
            }
            $this->setData();
        }

        public function setData()
        {
            $this->SetStatus(102);
            $outsideID = $this->ReadPropertyInteger('OutsideTemperatureID');
            $counterID = $this->ReadPropertyInteger('CounterID');

            if ($outsideID == 0 && $counterID == 0) {
                //No vars selected
                $this->SetStatus(202);
                return null;
            }

            if ($this->GetTimerInterval('UpdateCalculationVBV') < ($this->ReadPropertyInteger('Interval') * 1000 * 60)) {
                $this->SetTimerInterval('UpdateCalculationVBV', $this->ReadPropertyInteger('Interval') * 1000 * 60);
            }

            $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];

            $aggregationLevel = $this->ReadPropertyInteger('AggregationLevel');
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
                    $startTimeThisPeriod = 0;
                    $startTimeLastPeriod = 0;
            }

            //current period
            $list = $this->calculate($aggregationLevel, $outsideID, $counterID, $startTimeThisPeriod, $endTimeThisPeriod);
            $this->SetValue('CurrentPeriod', $list[0]);
            $this->SetValue('CurrentPercent', $list[1]);
            $this->SetValue('CurrentValue', $list[2]);

            //last period
            $list = $this->calculate($aggregationLevel, $outsideID, $counterID, $startTimeLastPeriod, $startTimeThisPeriod);
            $this->SetValue('LastPeriod', $list[0]);
            $this->SetValue('LastValue', $list[2]);
            $this->SetValue('LastPercent', $list[1]);
        }

        private function calculate(int $aggregationLevel, int $XID, int $YID, int $startTimePeriod, int $endTimePeriod)
        {
            $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
            $limit = $this->ReadPropertyInteger('Limit');
            if ($limit == 1) {
                //The limit must be higher than one or zero for all possible datasets
                $this->SetStatus(200);
            }

            //get xValues
            $rawX = AC_GetAggregatedValues($archiveID, $XID, $aggregationLevel, 0, $startTimePeriod, $limit);
            $xVarValues = [];
            foreach ($rawX as $dataset) {
                $xVarValues[] = $dataset['Avg'];
            }

            //get yValues
            $rawY = AC_GetAggregatedValues($archiveID, $YID, $aggregationLevel, 0, $startTimePeriod, $limit);
            $yVarValues = [];
            foreach ($rawY as $dataset) {
                $yVarValues[] = $dataset['Avg'];
            }

            //cut the Arrays for equal amount
            if (count($yVarValues) > count($xVarValues)) {
                $yVarValues = array_slice($yVarValues, 0, count($xVarValues));
            } else {
                $xVarValues = array_slice($xVarValues, 0, count($yVarValues));
            }

            //reverse Arrays for regression
            $valuesY = array_reverse($yVarValues);
            $valuesX = array_reverse($xVarValues);

            //Valid the values
            if (count($valuesY) <= 1) {
                $this->SetStatus(201);
                // The count of values is zero or one which leads to an error in the linear regression
                return null;
            }

            $parameter = $this->linearRegression($valuesX, $valuesY);
            $b = $parameter[0];
            $m = $parameter[1];

            //predict the Consum
            $currentX = AC_GetAggregatedValues($archiveID, $XID, $aggregationLevel, $startTimePeriod, $endTimePeriod, 0)[0];
            $predictionPeriod = $m * $currentX['Avg'] + $b;

            $currentY = AC_GetAggregatedValues($archiveID, $YID, $aggregationLevel, $startTimePeriod, $endTimePeriod - 1, 0)[0];
            $predictionFullPeriod = $currentY['Avg'] / $currentY['Duration'] * ($endTimePeriod - $startTimePeriod);
            $percent = ($predictionFullPeriod / $predictionPeriod) * 100;

            $currentY = $currentY['Avg'] * $currentY['Duration'];

            return [$predictionPeriod, $percent, $currentY];
        }

        private function linearRegression(array $valuesX, array $valuesY)
        {
            //reference https://de.wikipedia.org/wiki/Lineare_Einfachregression
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
            $measureOfDetermination = 1 - ($sqr / $sqt);
            return [$beta0, $beta1, $measureOfDetermination];
        }
    }