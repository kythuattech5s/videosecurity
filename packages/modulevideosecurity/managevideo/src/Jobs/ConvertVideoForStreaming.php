<?php

namespace modulevideosecurity\managevideo\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $itemTvsSecrets;
    public function __construct($itemTvsSecrets)
    {
        $this->itemTvsSecrets = $itemTvsSecrets;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filePath = $this->itemTvsSecrets->file_path.$this->itemTvsSecrets->file_name;
        if (!file_exists($filePath)) {
            $this->itemTvsSecrets->delete();
            return;
        }
        $fileInMediaDiskPath = str_replace('public/uploads/','',$filePath);
        $fileSavePath = $this->itemTvsSecrets->disk_path;
        $encryptionKey = \ProtoneMedia\LaravelFFMpeg\Exporters\HLSExporter::generateEncryptionKey();
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
            ->toDisk('tvsvideos')
            ->save($fileSavePath.$this->itemTvsSecrets->playlist_name);
            $this->itemTvsSecrets->converted = 1;
            $this->itemTvsSecrets->save();
        } catch (\ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException $e) {
            echo $e->getCommand();
            echo $e->getErrorOutput();
        }
        \FFMpeg::cleanupTemporaryFiles();
    }
}
