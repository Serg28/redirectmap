<?php

namespace Litvin\Redirectmap;

use Litvin\Redirectmap\Models\RedirectMap;
use Maatwebsite\Excel\Concerns\ToModel;

class RedirectImport implements ToModel
{
    public function model(array $row)
    {
        if (
            empty($row[0]) ||
            empty($row[1]) ||
            $row[0] === '#' ||
            $row[0] === 'Старе посилання'
        ) {
            return null;
        }

        if (count($row) === 4) {
            $oldLink = $row[1];
            $newLink = $row[2];
            $status  = $row[3] ?? 301;
        } else {
            $oldLink = $row[0];
            $newLink = $row[1];
            $status  = $row[2] ?? 301;
        }

        $redirect = RedirectMap::firstOrNew([
            'old_link' => $oldLink,
        ]);

        $redirect->fill([
            'new_link' => $newLink,
            'status'   => $status,
        ]);

        return $redirect;
    }
}
