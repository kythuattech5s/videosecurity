<?php

namespace modulevideosecurity\managevideo\Commands;

use Illuminate\Console\Command;
use \modulevideosecurity\managevideo\Models\TvsSecret;

class VideoConvert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tvsvideo:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Video m3u8';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $itemTvsSecrets = TvsSecret::where('converted',0)->get()->first();
        if (!isset($itemTvsSecrets)) return;
        $filePath = $itemTvsSecrets->file_path.$itemTvsSecrets->file_name;
        if (!file_exists($filePath)) {
            $itemTvsSecrets->delete();
            return;
        }
        $fileInMediaDiskPath = str_replace('public/uploads/','',$filePath);
        $fileSavePath = $itemTvsSecrets->disk_path;

        $encryptionKey = \ProtoneMedia\LaravelFFMpeg\Exporters\HLSExporter::generateEncryptionKey();
        $this->info("Start");
        $lowBitrate = (new \FFMpeg\Format\Video\X264())->setKiloBitrate(250);
        $midBitrate = (new \FFMpeg\Format\Video\X264())->setKiloBitrate(500);
        $highBitrate = (new \FFMpeg\Format\Video\X264())->setKiloBitrate(1000);
        $superBitrate = (new \FFMpeg\Format\Video\X264)->setKiloBitrate(1500);
        try {
            \FFMpeg::fromDisk('uploads')
            ->open($fileInMediaDiskPath)
            ->exportForHLS()
            ->withRotatingEncryptionKey(function ($filename, $contents) use ($fileSavePath) {
                \Storage::disk('tvsvideos')->put($fileSavePath.$filename, $contents);
            })
            ->setSegmentLength(10)
            ->addFormat($lowBitrate, function($media) {
                $media->addFilter('scale=640:480');
            })
            ->addFormat($midBitrate, function($media) {
                $media->scale(960, 720);
            })
            ->addFormat($highBitrate, function ($media) {
                $media->addFilter(function ($filters, $in, $out) {
                    $filters->custom($in, 'scale=1920:1200', $out);
                });
            })
            ->addFormat($superBitrate, function($media) {
                $media->addLegacyFilter(function ($filters) {
                    $filters->resize(new \FFMpeg\Coordinate\Dimension(2560, 1920));
                });
            })
            ->onProgress(function($process){
                $this->info("Process:{$process}%");
            })
            ->toDisk('tvsvideos')
            ->save($fileSavePath.$itemTvsSecrets->playlist_name);
            $itemTvsSecrets->converted = 1;
            $itemTvsSecrets->save();
        } catch (\ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException $e) {
            echo $e->getCommand();
            echo $e->getErrorOutput();
        }
        \FFMpeg::cleanupTemporaryFiles();
        $this->info("End");
    }
}
