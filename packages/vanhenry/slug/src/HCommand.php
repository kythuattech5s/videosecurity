<?php 
namespace vanhenry\slug;
use Illuminate\Console\GeneratorCommand;
class HCommand extends GeneratorCommand{
	protected $name="make:slugmodel";
	protected $description = 'Create new custom Model by VTH';
	protected $type = 'Model';
	protected function getStub()
    {
        return __DIR__.'/stubs/model.stub';
    }

}

?>