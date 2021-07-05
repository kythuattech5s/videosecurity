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
    protected $itemMedia;
    public function __construct($itemMedia)
    {
        $this->itemMedia = $itemMedia;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $encryptionKey = \ProtoneMedia\LaravelFFMpeg\Exporters\HLSExporter::generateEncryptionKey();
        $lowBitrate = (new \FFMpeg\Format\Video\X264())->setKiloBitrate(250);
        $midBitrate = (new \FFMpeg\Format\Video\X264())->setKiloBitrate(500);
        $highBitrate = (new \FFMpeg\Format\Video\X264())->setKiloBitrate(1000);
        $superBitrate = (new \FFMpeg\Format\Video\X264)->setKiloBitrate(1500);
        try {
            \FFMpeg::fromDisk('tvsvideos')
            ->open('2.mp4')
            ->exportForHLS()
            ->withRotatingEncryptionKey(function ($filename, $contents) {
                \Storage::disk('tvsvideos')->put(\VideoSetting::getSettingConfig('path_output_folder')."/".$filename, $contents);
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
            ->save(\VideoSetting::getSettingConfig('path_output_folder').'/1.m3u8');
        } catch (\ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException $e) {
            echo $e->getCommand();
            echo $e->getErrorOutput();
        }
        \FFMpeg::cleanupTemporaryFiles();
    }
}
