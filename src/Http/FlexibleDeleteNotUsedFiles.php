<?php

namespace Whitecube\NovaFlexibleContent\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class FlexibleDeleteNotUsedFiles
{
    /**
     * File to delete request key
     *
     * @var string
     */
    const FILES_TO_DELETE = '___nova_flexible_content_files_to_delete';

    /**
     * The files to delete
     *
     * @var array
     */
    private $filesToDelete;

    /**
     * The components to disk map
     *
     * @var array
     */
    private static $fileDiskMap = [];



    /**
     * @param  string $filesToDelete
     * @return void
     */
    public function __construct($filesToDelete)
    {
        $this->filesToDelete = @json_decode($filesToDelete) ?? [];
    }

    /**
     * @param  string $filesToDelete
     * @return self
     */
    public static function make($filesToDelete)
    {
        return new static($filesToDelete);
    }

    /**
     * Delete the files
     *
     * @return void
     */
    public function deleteFiles()
    {
        foreach($this->filesToDelete as $file) {
            $attribute = substr($file->attribute, strlen($file->key.FlexibleAttribute::GROUP_SEPARATOR));
            $mapKey = sprintf('%s.%s', $file->layout, $attribute);
            if (isset(self::$fileDiskMap[$mapKey])) {
                Storage::disk(self::$fileDiskMap[$mapKey])->delete($file->value);
            }
        }
    }

    /**
     * @param array $fileDiskMap
     * @return void
     */
    public static function setFilesDiskMap($fileDiskMap) {
        self::$fileDiskMap = $fileDiskMap;
    }

}
