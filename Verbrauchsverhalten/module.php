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
            //outside temperature is the x
            $this->RegisterPropertyInteger('OutsideTemperatureID', 0);
            //coutner is the y
            $this->RegisterPropertyInteger('CounterID', 0);
            

            //variables
            $this->RegisterVariableFloat('CurrentPeriod', $this->Translate('Prediction of the current Period'));
            $this->RegisterVariableFloat('LastPeriod', $this->Translate('Prediction of the last Period'));
            $this->RegisterVariableFloat('Procent', $this->Translate('Procent'));

            //Timer
            $this->RegisterPropertyInteger('Interval', 5);
            $this->RegisterTimer('UpdateCalculationVBV', 0, 'VBV_setData($_IPS[\'TARGET\']);');
        }

        public function ApplyChanges()
        {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->setData();
        }

        public function setData()
        {
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

            $list = $this->calculate($aggregationLevel, $outsideID, $counterID, $startTimeThisPeriod, $endTimeThisPeriod);

            //current period
            $this->SetValue('CurrentPeriod', $list[0]);
            $this->SetValue('Procent', $list[1]);

            //last period
            $list = $this->calculate($aggregationLevel, $outsideID, $counterID, $startTimeLastPeriod, $startTimeThisPeriod);
            $this->SetValue('LastPeriod', $list[0]);
        }

        private function calculate(int $aggregationLevel, int $XID, int $YID, int $startTimePeriod, int $endTimePeriod)
        {
            $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];

            //get xValues
            $rawX = AC_GetAggregatedValues($archiveID, $XID, $aggregationLevel, 0, $startTimePeriod - 1, 0);
            $xVarValues = [];
            foreach ($rawX as $dataset) {
                $xVarValues[] = $dataset['Avg'];
            }
            $valuesX = array_reverse($xVarValues);

            //get yValues
            $rawY = AC_GetAggregatedValues($archiveID, $YID, $aggregationLevel, 0, $startTimePeriod - 1, 0);
            $yVarValues = [];
            foreach ($rawY as $dataset) {
                $yVarValues[] = $dataset['Avg'];
            }
            $valuesY = array_reverse($yVarValues);

            //Valid the values
            if (count($valuesX) != count($valuesY)) {
                $this->SetStatus(200);
                // The amount of values is not the same for both axis
                return null;
            } elseif (count($valuesY) <= 1) {
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

            
            $currentY = AC_GetAggregatedValues($archiveID, $YID, $aggregationLevel, $startTimePeriod, $endTimePeriod, 0)[0];
            $predictionFullPeriod = $currentY['Avg'] / $currentY['Duration'] * ($endTimePeriod - $startTimePeriod);
            $procent = ($predictionFullPeriod / $predictionPeriod) * 100;
            return [$predictionPeriod, $procent];
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