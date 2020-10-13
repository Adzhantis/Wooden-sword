<?php

namespace App;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AutoGroupState
{
    /**
     * array [ id_group => weight]
     */
    const WEIGHT = [
        2 => 3,
        3 => 4,
        4 => 6,
    ];

    const FILE   = 'autoGroupsState.txt';

    /**
     * @return array
     */
    public function get(): \stdClass
    {
        if (!Storage::disk('local')->exists(self::FILE)) {
            $this->reset();
        }

        $autoGroupsStateText = Storage::disk('local')->get(self::FILE);
        return json_decode($autoGroupsStateText);
    }

    /**
     *
     */
    public function reset(): void
    {
        $stateData['values'] = $this->getValuesForGroupIds();

        foreach (self::WEIGHT as $id_group => $weightSingle) {
            $stateData['autoGroups'][$id_group] = [
                'weight'             => $weightSingle,
                'countPlayer'        => 0,
                'countPlayerPercent' => 0,
            ];
        }

        $stateData['total'] = [
            'weight'      => array_sum(self::WEIGHT),
            'countPlayer' => 0,
        ];


        Storage::disk('local')->put(self::FILE, json_encode($stateData));
    }

    /**
     * @return int
     */
    public function generateIdGroup(): int
    {
        if (!Storage::disk('local')->exists(self::FILE)) {
            return 0;
        }

        $autoGroupsState = $this->get();
        $values = $this->getValues();

        $chosen        = array_rand($values);
        $chosenGroupId = $values[$chosen];
        unset($values[$chosen]);

        $chosenGroup = $autoGroupsState->autoGroups->$chosenGroupId;
        $chosenGroup->countPlayer += 1;
        $autoGroupsState->total->countPlayer += 1;

        $autoGroupsState->autoGroups->$chosenGroupId = $chosenGroup;

        $autoGroupsState->values = $values;

        Storage::disk('local')->put(self::FILE, json_encode($autoGroupsState));

        return (int) $chosenGroupId;
    }

    /**
     * @return array
     */
    private function getValuesForGroupIds(): array
    {
        $values = [];
        $sum = 0;
        $i = 1;

        foreach (self::WEIGHT as $idGroup => $groupValuesCount) {
            $sum += $groupValuesCount;
            do {
                $values[$i] = $idGroup;
                $i++;

            } while ($i <= $sum);
        }
        return $values;
    }

    /**
     * @return array
     */
    private function getValues(): array
    {
        $values = (array) $this->get()->values;

        if (empty($values)) {
            $this->reset();
            $values = (array) $this->get()->values;
        }
        return $values;
    }
}