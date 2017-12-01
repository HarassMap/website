<?php

namespace Harassmap\Incidents\Classes;

use RainLab\Pages\Classes\PageList as StaticPageList;

class PageList extends StaticPageList
{

    protected function removePages($originalData, $structure)
    {
        foreach ($structure as $key => $value) {
            unset($originalData[$key]);

            if (!empty($value)) {
                $originalData = $this->removePages($originalData, $value);
            }
        }

        return $originalData;
    }

    public function updateStructurePart($structure)
    {
        $originalData = $this->getPagesConfig();
        $originalData['static-pages'] = $this->removePages($originalData['static-pages'], $structure);
        $originalData['static-pages'] = $originalData['static-pages'] + $structure;

        $this->updateStructure($originalData['static-pages']);
    }

}