<?php namespace vanhenry\slug;
use Illuminate\Database\Eloquent\Model;
class HModel extends Model implements  HSlugInterface
{
    use HSlugTrait;
 
}
?>