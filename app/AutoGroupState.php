<?php

namespace App;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AutoGroupState
{
    const WEIGHT = [1,2,7];
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
        $weightSum      = array_sum(self::WEIGHT);
        $weightCount    = count(self::WEIGHT);

        $groups = DB::table('groups')->limit($weightCount)->get()->toArray();

        $stateData['values'] = $this->getValuesForGroupIds($groups);

        foreach (self::WEIGHT as $weightSingle) {
            $group = array_shift($groups);
            $stateData['autoGroups'][$group->id] = [
                'weight'             => $weightSingle,
                'weightPercent'      => $weightSingle / $weightSum,
                'countPlayer'        => 0,
                'countPlayerPercent' => 0,
            ];
        }

        $stateData['total'] = [
            'weight'      => $weightSum,
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

        //get  player percent
        foreach ($autoGroupsState->autoGroups as $idGroup => $autoGroup) {
            $autoGroup->countPlayerPercent = $autoGroup->countPlayer / $autoGroupsState->total->countPlayer;
        }

        $autoGroupsState->autoGroups->$chosenGroupId = $chosenGroup;

        $autoGroupsState->values = $values;

        Storage::disk('local')->put(self::FILE, json_encode($autoGroupsState));

        return (int) $chosenGroupId;
    }

    /**
     * @param array $groups
     * @return array
     */
    private function getValuesForGroupIds(array $groups): array
    {
        $values = [];
        $sum = 0;
        $i = 1;

        foreach (self::WEIGHT as $key => $groupValuesCount) {
            $sum += $groupValuesCount;
            do {
                $values[$i] = $groups[$key]->id;
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