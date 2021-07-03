<?php echo '<?xml version="1.0" encoding="UTF-8"?>'?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($listSitemaps as $sitemap)
   <sitemap>
      <loc>{{url('sitemap/'.$sitemap->table.'/'.$sitemap->y."-".$sitemap->m)}}.xml</loc>
      <lastmod>{{date_create_from_format("Y-m-d H:i:s",$sitemap->created_at)->format("Y-m-d\TH:i:sP")}}</lastmod>
   </sitemap>
@endforeach
<sitemap>
      <loc>{{url('sitemap/static.xml')}}</loc>
      <lastmod>{{(new \DateTime())->format("Y-m-d\TH:i:sP")}}</lastmod>
   </sitemap>
</sitemapindex>