<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';
include_once __DIR__ . '/stubs/ConstantStubs.php';

use PHPUnit\Framework\TestCase;

if (!defined('KR_READY')) {
    define('KR_READY', 10103);
}
if (!defined('VM_UPDATE')) {
    define('VM_UPDATE', 10603);
}

class VerbrauchsverhaltenBaseTest extends TestCase
{
    protected $ArchiveControlID;
    protected $Verbrauchsverhalten;

    protected function setUp(): void
    {
        //Reset
        IPS\Kernel::reset();

        //Register our core stubs for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/stubs/CoreStubs/library.json');

        //Register our library we need for testing
        IPS\ModuleLoader::loadLibrary(__DIR__ . '/../library.json');

        IPS_CreateVariableProfile('~Valve.F', 2);

        //Create instances
        $this->ArchiveControlID = IPS_CreateInstance('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
        $this->Verbrauchsverhalten = IPS_CreateInstance('{EE2368AF-2347-4F90-31B8-861861F060DE}');

        parent::setUp();
    }

    public function testDay()
    {
        //Instance
        $archiveID = $this->ArchiveControlID;
        $instanceID = $this->Verbrauchsverhalten;

        //Create Float Variables
        $outsideID = IPS_CreateVariable(2);
        $counterID = IPS_CreateVariable(2);

        AC_SetLoggingStatus($archiveID, $outsideID, true);
        AC_SetLoggingStatus($archiveID, $counterID, true);

        VBV_setTime($instanceID, strtotime('May 24 1971 12:00'));

        IPS_EnableDebug($instanceID, 600);

        //SetValues
        $aggregatedValueOutside = [
            [
                'Avg'       => 15,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 11:00:00')
            ],
            [
                'Avg'       => 12,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 10:00:00')
            ],
            [
                'Avg'       => 10,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 9:00:00')
            ],
            [
                'Avg'       => 9,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 8:00:00')
            ],
            [
                'Avg'       => 7,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 7:00:00')
            ],
            [
                'Avg'       => 15,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 11:00:00')
            ],
            [
                'Avg'       => 12,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 10:00:00')
            ],
            [
                'Avg'       => 10,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 9:00:00')
            ],
            [
                'Avg'       => 9,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 8:00:00')
            ],
            [
                'Avg'       => 7,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 7:00:00')
            ],
            [
                'Avg'       => 15,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 11:00:00')
            ],
            [
                'Avg'       => 12,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 10:00:00')
            ],
            [
                'Avg'       => 10,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 9:00:00')
            ],
            [
                'Avg'       => 9,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 8:00:00')
            ],
            [
                'Avg'       => 7,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 7:00:00')
            ],
        ];
        $aggregatedValueCounter = [
            [
                'Avg'       => 21.5,
                'Duration'  => 1 * 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 11:00:00')
            ],
            [
                'Avg'       => 20,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 10:00:00')
            ],
            [
                'Avg'       => 19,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 9:00:00')
            ], [
                'Avg'       => 18.5,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 8:00:00')
            ], [
                'Avg'       => 17.5,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 24 1971 7:00:00')
            ],
            [
                'Avg'       => 21.5,
                'Duration'  => 1 * 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 11:00:00')
            ],
            [
                'Avg'       => 20,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 10:00:00')
            ],
            [
                'Avg'       => 19,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 9:00:00')
            ], [
                'Avg'       => 18.5,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 8:00:00')
            ], [
                'Avg'       => 17.5,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 7:00:00')
            ],
            [
                'Avg'       => 21.5,
                'Duration'  => 1 * 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 11:00:00')
            ],
            [
                'Avg'       => 20,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 10:00:00')
            ],
            [
                'Avg'       => 19,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 9:00:00')
            ], [
                'Avg'       => 18.5,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 8:00:00')
            ], [
                'Avg'       => 17.5,
                'Duration'  => 60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 7:00:00')
            ],
        ];

        AC_StubsAddAggregatedValues($archiveID, $outsideID, 0, $aggregatedValueOutside);
        AC_StubsAddAggregatedValues($archiveID, $counterID, 0, $aggregatedValueCounter);

        $valuesOutside = AC_GetAggregatedValues($archiveID, $outsideID, 0, strtotime('May 24 1971 00:00'), strtotime('May 25 1971 00:00') - 1, 0);
        $this->assertEquals(5, count($valuesOutside));

        //Configurations
        $configuration = json_encode([
            'CounterID'            => $counterID,
            'OutsideTemperatureID' => $outsideID,
            'Limit'                => 0,
            'Period'               => 1, //Day
            'Interval'             => 5
        ]);
        IPS_SetConfiguration($instanceID, $configuration);
        IPS_ApplyChanges($instanceID);

        $this->assertEquals(102, IPS_GetInstance($instanceID)['InstanceStatus']);
        
        //Test pairs
        $testMatrix = [
            //current
            [96.5, GetValue(IPS_GetObjectIDByIdent('CurrentValue', $instanceID)), 'CurrentValue'],
            [193, GetValue(IPS_GetObjectIDByIdent('CurrentForecast', $instanceID)), 'CurrentForecast'],
            [96.5, round(GetValue(IPS_GetObjectIDByIdent('CurrentPrediction', $instanceID)), 7), 'CurrentPrediction'],
            [200, round(GetValue(IPS_GetObjectIDByIdent('CurrentPercent', $instanceID)), 7), 'CurrentPercent'],
            [1, round(GetValue(IPS_GetObjectIDByIdent('CurrentCoD', $instanceID)), 7), 'CurrentCoD'],
            //last
            [96.5, GetValue(IPS_GetObjectIDByIdent('LastValue', $instanceID)), 'LastValue'],
            [64.3333333, round(GetValue(IPS_GetObjectIDByIdent('LastForecast', $instanceID)), 7), 'LastForecast'],
            [96.5, round(GetValue(IPS_GetObjectIDByIdent('LastPrediction', $instanceID)), 7), 'LastPrediction'],
            [66.6666667, round(GetValue(IPS_GetObjectIDByIdent('LastPercent', $instanceID)), 7), 'LastPercent'],
            [1, round(GetValue(IPS_GetObjectIDByIdent('LastCoD', $instanceID)), 7), 'LastCoD']
        ];

        foreach ($testMatrix as $set) {
            $this->assertEquals($set[0], $set[1], $set[2]);
        }
    }

    public function testMonth()
    {
        //Instance
        $archiveID = $this->ArchiveControlID;
        $instanceID = $this->Verbrauchsverhalten;

        //Create Float Variables
        $outsideID = IPS_CreateVariable(2);
        $counterID = IPS_CreateVariable(2);
        AC_SetLoggingStatus($archiveID, $outsideID, true);
        AC_SetLoggingStatus($archiveID, $counterID, true);

        VBV_setTime($instanceID, strtotime('May 24 1971 12:00'));
        IPS_EnableDebug($instanceID, 600);

        $aggregatedValueCounter = [
            [
                'Avg'       => 6.75,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 00:00:00')
            ],
            [
                'Avg'       => 4.75,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 00:00:00')
            ],
            [
                'Avg'       => 5.75,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 21 1971 00:00:00')
            ],
            [
                'Avg'       => 4.5,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 20 1971 00:00:00')
            ],
            [
                'Avg'       => 0,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 19 1971 00:00:00')
            ],[
                'Avg'       => 6.75,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 23 1971 00:00:00')
            ],
            [
                'Avg'       => 4.75,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 22 1971 00:00:00')
            ],
            [
                'Avg'       => 5.75,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 21 1971 00:00:00')
            ],
            [
                'Avg'       => 4.5,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 20 1971 00:00:00')
            ],
            [
                'Avg'       => 0,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 19 1971 00:00:00')
            ],
            [
                'Avg'       => 6.75,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 23 1971 00:00:00')
            ],
            [
                'Avg'       => 4.75,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 22 1971 00:00:00')
            ],
            [
                'Avg'       => 5.75,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 21 1971 00:00:00')
            ],
            [
                'Avg'       => 4.5,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 20 1971 00:00:00')
            ],
            [
                'Avg'       => 0,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 19 1971 00:00:00')
            ],
        ];
        $aggregatedValueOutside = [
            [
                'Avg'       => 1,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 23 1971 00:00:00')
            ],
            [
                'Avg'       => 9,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 22 1971 00:00:00')
            ],
            [
                'Avg'       => 5,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 21 1971 00:00:00')
            ],
            [
                'Avg'       => 10,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 20 1971 00:00:00')
            ],
            [
                'Avg'       => 36,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('May 19 1971 00:00:00')
            ],
            [
                'Avg'       => 1,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 23 1971 00:00:00')
            ],
            [
                'Avg'       => 9,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 22 1971 00:00:00')
            ],
            [
                'Avg'       => 5,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 21 1971 00:00:00')
            ],
            [
                'Avg'       => 10,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 20 1971 00:00:00')
            ],
            [
                'Avg'       => 36,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('April 19 1971 00:00:00')
            ], [
                'Avg'       => 1,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 23 1971 00:00:00')
            ],
            [
                'Avg'       => 9,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 22 1971 00:00:00')
            ],
            [
                'Avg'       => 5,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 21 1971 00:00:00')
            ],
            [
                'Avg'       => 10,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 20 1971 00:00:00')
            ],
            [
                'Avg'       => 36,
                'Duration'  => 24 *60 * 60,
                'Max'       => 0,
                'MaxTime'   => 0,
                'Min'       => 0,
                'MinTime'   => 0,
                'TimeStamp' => strtotime('March 19 1971 00:00:00')
            ],
        ];
        AC_StubsAddAggregatedValues($archiveID, $outsideID, 1, $aggregatedValueOutside);
        AC_StubsAddAggregatedValues($archiveID, $counterID, 1, $aggregatedValueCounter);

        $valuesOutside = AC_GetAggregatedValues($archiveID, $outsideID, 1, strtotime('May 1 1971 00:00'), strtotime('May 24 1971 00:00') - 1, 0);
        $this->assertEquals(5, count($valuesOutside));

        //Configurations
        $configuration = json_encode([
            'CounterID'            => $counterID,
            'OutsideTemperatureID' => $outsideID,
            'Limit'                => 0,
            'Period'               => 3, //Month
            'Interval'             => 5
        ]);
        IPS_SetConfiguration($instanceID, $configuration);
        IPS_ApplyChanges($instanceID);
        
        $this->assertEquals(102, IPS_GetInstance($instanceID)['InstanceStatus']);

        //Test pairs
        $testMatrix = [
            //current
            [21.75, GetValue(IPS_GetObjectIDByIdent('CurrentValue', $instanceID)), 'CurrentValue'],
            [28.6914894, round(GetValue(IPS_GetObjectIDByIdent('CurrentForecast', $instanceID)), 7), 'CurrentForecast'],
            [21.8570111, round(GetValue(IPS_GetObjectIDByIdent('CurrentPrediction', $instanceID)), 7), 'CurrentPrediction'],
            [131.2690435, round(GetValue(IPS_GetObjectIDByIdent('CurrentPercent', $instanceID)), 7), 'CurrentPercent'],
            [0.9920215, round(GetValue(IPS_GetObjectIDByIdent('CurrentCoD', $instanceID)), 7), 'CurrentCoD'],
            //last
            [21.75, GetValue(IPS_GetObjectIDByIdent('LastValue', $instanceID)), 'LastValue'],
            [12.1962617, round(GetValue(IPS_GetObjectIDByIdent('LastForecast', $instanceID)), 7), 'LastForecast'],
            [21.8570111, round(GetValue(IPS_GetObjectIDByIdent('LastPrediction', $instanceID)), 7), 'LastPrediction'],
            [55.8002265, round(GetValue(IPS_GetObjectIDByIdent('LastPercent', $instanceID)), 7), 'LastPercent'],
            [0.9920215, round(GetValue(IPS_GetObjectIDByIdent('LastCoD', $instanceID)), 7), 'LastCoD']
        ];

        foreach ($testMatrix as $set) {
            $this->assertEquals($set[0], $set[1], $set[2]);
        }
    }
}