<?php

namespace Adrenth\RedirectLite\Classes;

use Adrenth\RedirectLite\Models\Redirect;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use League\Csv\Writer;
use Log;
use October\Rain\Support\Traits\Singleton;

/**
 * Class PublishManager
 *
 * @package Adrenth\RedirectLite\Classes
 */
class PublishManager
{
    use Singleton;

    /** @var string */
    private $redirectsFile;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->redirectsFile = storage_path('app/redirects-lite.csv');
    }

    /**
     * Publish applicable redirects.
     *
     * @return int Number of published redirects
     */
    public function publish()
    {
        if (file_exists($this->redirectsFile)) {
            unlink($this->redirectsFile);
        }

        $columns = [
            'id',
            'from_url',
            'to_url',
            'status_code',
        ];

        /** @var Collection $redirects */
        $redirects = Redirect::query()
            ->orderBy('sort_order')
            ->get($columns);

        try {
            $writer = Writer::createFromPath($this->redirectsFile, 'w+');
            $writer->insertOne($columns);

            foreach ($redirects->toArray() as $row) {
                $writer->insertOne($row);
            }
        } catch (Exception $e) {
            Log::critical($e);
        }

        return $redirects->count();
    }
}
