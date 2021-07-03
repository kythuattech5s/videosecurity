<?php
namespace vanhenry\manager\pagination;
use Illuminate\Support\HtmlString;
use Illuminate\Pagination\BootstrapThreePresenter;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Pagination\UrlWindow;
class HBootstrapThreePresenter extends BootstrapThreePresenter {
	public function __construct(PaginatorContract $paginator, UrlWindow $window = null)
    {
        $this->paginator = $paginator;
        $this->window = is_null($window) ? HWindowUrl::make($paginator) : $window->get();
    }
	protected function getDots()
    {
        return "";
    }
    public function render()
    {
        if ($this->hasPages()) {
            return new HtmlString(sprintf(
                '<ul class="te-pagination">%s</ul>',
                $this->getLinks()
            ));
        }
        return '';
    }
    protected function getActivePageWrapper($text)
    {
        return '<strong>'.$text.'</strong>';
    }
    protected function getAvailablePageWrapper($url, $page, $rel = null)
    {
        $rel = is_null($rel) ? '' : ' rel="'.$rel.'"';
        return '<a href="'.htmlentities($url).'"'.$rel.'>'.$page.'</a>';
    }
}
