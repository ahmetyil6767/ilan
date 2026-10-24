<?php
/**
 * SEO Optimizasyon Modülü - JaayQuick
 * Bu dosya header.php'de include edilmelidir.
 */

if (!defined('JAÄYQUICK_SEO_LOADED')) {
    define('JAÄYQUICK_SEO_LOADED', true);
}

/**
 * Sayfa türünü belirler
 */
function seo_get_page_type() {
    $php_self = $_SERVER["PHP_SELF"];
    $request_uri = $_SERVER["REQUEST_URI"];
    
    // DEBUG
    error_log('[SEO] PHP_SELF: ' . $php_self . ' | REQUEST_URI: ' . $request_uri);
    
    // Check for rewritten ad URL pattern: /{slug}-{id} where id is numeric
    if (preg_match('/^\/([^\/]+)-(\d+)(?:\/|$)/', $request_uri, $m)) {
        error_log('[SEO] Matched ad pattern, id: ' . $m[2]);
        $icerik_id = $m[2];
        $icerik_tip = bilgi_getir("icerik", "tip", $icerik_id);
        return array('type' => 'ad', 'id' => $icerik_id, 'tip' => $icerik_tip);
    }
    
    if (strstr($php_self, 'icerik.php')) {
        $icerik_id = @$_GET["id"];
        $icerik_tip = bilgi_getir("icerik", "tip", @$_GET["id"]);
        return array('type' => 'ad', 'id' => $icerik_id, 'tip' => $icerik_tip);
    }
    if (strstr($_SERVER["PHP_SELF"], 'kategori.php')) {
        return array('type' => 'category', 'id' => @$_GET["id"]);
    }
    if (strstr($_SERVER["PHP_SELF"], 'magaza.php')) {
        return array('type' => 'store', 'id' => @$_GET["id"]);
    }
    if (strstr($_SERVER["PHP_SELF"], 'profil.php')) {
        return array('type' => 'profile', 'id' => @$_GET["id"]);
    }
    if (strstr($_SERVER["PHP_SELF"], 'ara.php')) {
        return array('type' => 'search');
    }
    if (strstr($_SERVER["PHP_SELF"], 'index.php')) {
        $sayfa = @$_GET["p"];
        if ($sayfa) {
            return array('type' => 'page', 'slug' => @$_GET["p"]);
        }
        return array('type' => 'home');
    }
    if (strstr($_SERVER["PHP_SELF"], 'ara.php')) {
        return array('type' => 'search');
    }
    if (strstr($_SERVER["PHP_SELF"], '404.php')) {
        return array('type' => '404');
    }
    return array('type' => 'unknown');
}

/**
 * JSON-LD Structured Data üretir
 */
function seo_json_ld() {
    $page = seo_get_page_type();
    $site_url = site_url();
    $site_name = ayar_getir('site_baslik');
    $json = '';
    
    // Organization - Her sayfada
    $org = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $site_name,
        'url' => $site_url,
        'logo' => $site_url . '/' . ltrim(tema_ayar('logo'), '/'),
        'sameAs' => array(
            'https://www.facebook.com/' . strtolower($site_name),
            'https://twitter.com/' . strtolower($site_name),
        ),
        'address' => array(
            '@type' => 'PostalAddress',
            'addressCountry' => 'GM',
        ),
        'contactPoint' => array(
            '@type' => 'ContactPoint',
            'telephone' => '+220-267-7408',
            'contactType' => 'customer service',
            'availableLanguage' => array('English', 'French', 'Arabic'),
        ),
    );
    $json .= '<script type="application/ld+json">' . json_encode($org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    
    // WebSite - Arama kutusu
    $website = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $site_name,
        'url' => $site_url,
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => array(
                '@type' => 'EntryPoint',
                'urlTemplate' => $site_url . '/ara/{search_term_string}',
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );
    $json .= '<script type="application/ld+json">' . json_encode($website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    
    $page_type = seo_get_page_type();
    
    // Sayfa türüne göre structured data
    switch ($page_type['type']) {
        case 'ad':
            $json .= seo_json_ld_ad($page['id'], $page['tip']);
            break;
        case 'category':
            $json .= seo_json_ld_category($page['id']);
            break;
        case 'store':
            $json .= seo_json_ld_store($page['id']);
            break;
        case 'profile':
            $json .= seo_json_ld_profile($page['id']);
            break;
        case 'search':
            $json .= seo_json_ld_search();
            break;
        case 'home':
            $json .= seo_json_ld_home();
            break;
    }
    
    // BreadcrumbList - Her sayfada (home hariç)
    if ($page_type['type'] !== 'home') {
        $json .= seo_json_ld_breadcrumb();
    }
    
    return $json;
}

/**
 * İlan (Ad) için Product schema
 */
function seo_json_ld_ad($id, $tip) {
    $icerik = bilgi_getir('icerik', 'icerik', $id);
    $baslik = _DIL_BASLIK(bilgi_getir('icerik', 'baslik', $id), $id, $tip);
    // Fiyat ekstra_bilgi tablosunda anahtar='fiyat' olarak saklanir
    global $baglan;
    $fiyat = NULL;
    $r = mysqli_query($baglan, "SELECT deger FROM ekstra_bilgi WHERE id='$id' AND anahtar='fiyat' LIMIT 1");
    if ($r) {
        $row = mysqli_fetch_assoc($r);
        if ($row) { $fiyat = $row['deger']; }
    }
    if (!$fiyat) { $fiyat = 0; }
    $para_birim = aktif_birim();
    $resim = resim_getir('1200_630', $id);
    $tarih = bilgi_getir('icerik', 'tarih_saat', $id);
    $kategori_id = bilgi_getir('icerik', 'kategori', $id);
    $kategori_adi = _DIL_BASLIK(kategori_adi_getir($kategori_id), $kategori_id, 'kategori');
    // il/ilce ekstra_bilgi tablosunda anahtar='il' ve 'ilce' olarak saklanir
    $il = NULL;
    $ilce = NULL;
    $r = mysqli_query($baglan, "SELECT deger FROM ekstra_bilgi WHERE id='$id' AND anahtar='il' LIMIT 1");
    if ($r) { $row = mysqli_fetch_assoc($r); if ($row) { $il = $row['deger']; } }
    $r = mysqli_query($baglan, "SELECT deger FROM ekstra_bilgi WHERE id='$id' AND anahtar='ilce' LIMIT 1");
    if ($r) { $row = mysqli_fetch_assoc($r); if ($row) { $ilce = $row['deger']; } }
    $kullanici_id = bilgi_getir('icerik', 'yazar', $id);
    $yazar_ad = getir('uyeler', 'ad_soyad', $kullanici_id);
    $ilan_url = link_getir($id);
    $resimler = galeri_getir($id);
    
    $product = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $baslik,
        'description' => strip_tags(mb_substr($icerik, 0, 500)) . '...',
        'url' => link_getir($id),
        'image' => $resim ? array($resim) : array(),
        'sku' => 'AD-' . $id,
        'mpn' => 'AD-' . $id,
        'brand' => array(
            '@type' => 'Brand',
            'name' => ayar_getir('site_baslik'),
        ),
        'category' => $kategori_adi,
        'offers' => array(
            '@type' => 'Offer',
            'url' => link_getir($id),
            'priceCurrency' => $para_birim,
            'price' => is_numeric($fiyat) ? (float)$fiyat : 0,
            'availability' => 'https://schema.org/InStock',
            'seller' => array(
                '@type' => 'Person',
                'name' => $yazar_ad,
            ),
            'priceValidUntil' => date('Y-m-d', strtotime('+30 days')),
        ),
    );
    
    if ($resimler) {
        $images = array();
        foreach ($resimler as $r) {
            $images[] = $r['resim'];
        }
        $product['image'] = $images;
    }
    
    // AggregateRating - eğer yorum varsa
    // $yorum_sayisi = yorum_sayisi($id);
    // if ($yorum_sayisi > 0) { ... }
    
    if (!empty($tarih)) {
        $product['datePublished'] = date('c', strtotime($tarih));
    }
    
    // Adres bilgisi
    if ($il || $ilce) {
        $product['availableAtOrFrom'] = array(
            '@type' => 'Place',
            'address' => array(
                '@type' => 'PostalAddress',
                'addressLocality' => $ilce ? getir('ilceler', 'baslik', $ilce) : '',
                'addressRegion' => $il ? getir('iller', 'baslik', $il) : '',
                'addressCountry' => 'GM',
            ),
        );
    }
    
    return '<script type="application/ld+json">' . json_encode($product, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Kategori için CollectionPage schema
 */
function seo_json_ld_category($id) {
    $kategori_adi = _DIL_BASLIK(kategori_adi_getir($id), $id, 'kategori');
    $aciklama = ekstra_bilgi_getir('aciklama', $id, 'kategori');
    $url = kategori_link_getir($id);
    
    $collection = array(
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $kategori_adi,
        'description' => $aciklama ?: site_aciklama_getir(),
        'url' => $url,
        'mainEntity' => array(
            '@type' => 'ItemList',
            'name' => $kategori_adi,
            'description' => $aciklama ?: site_aciklama_getir(),
        ),
    );
    
    return '<script type="application/ld+json">' . json_encode($collection, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Mağaza için Store schema
 */
function seo_json_ld_store($id) {
    $magaza_id = $id;
    if (!$magaza_id) {
        $magaza_url = @$_GET["url"];
        $magaza_id = id_getir("magazalar", "subdomain", $magaza_url);
    }
    if (!$magaza_id) return '';
    
    $baslik = getir("magazalar", "baslik", $magaza_id);
    $alt_domain = getir("magazalar", "subdomain", $magaza_id);
    $url = magaza_link_getir($magaza_id);
    $aciklama = getir("magazalar", "aciklama", $magaza_id);
    $profil_resim = magaza_bilgi("profil_gorseli", $magaza_id);
    
    $store = array(
        '@context' => 'https://schema.org',
        '@type' => 'Store',
        'name' => $baslik,
        'description' => $aciklama ?: site_aciklama_getir(),
        'url' => $url,
        'image' => $profil_resim && $profil_resim != '-150_150.' ? thumbnail_url('1200_630', $profil_resim) : '',
        'address' => array(
            '@type' => 'PostalAddress',
            'addressCountry' => 'GM',
        ),
    );
    
    return '<script type="application/ld+json">' . json_encode($store, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Profil için Person schema
 */
function seo_json_ld_profile($id) {
    $ad = bilgi_getir("uyeler", "ad_soyad", $id);
    $url = uye_link_getir($id);
    $profil_resim = uye_bilgi("profil_gorseli", $id);
    $kayit_tarihi = getir("uyeler", "tarih_saat", $id);
    
    $person = array(
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $ad,
        'url' => uye_link_getir($id),
        'image' => $profil_resim && $profil_resim != '-150_150.' ? thumbnail_url('250_250', $profil_resim) : '',
    );
    
    if ($kayit_tarihi) {
        $person['dateCreated'] = date('c', strtotime($kayit_tarihi));
    }
    
    return '<script type="application/ld+json">' . json_encode($person, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Arama sayfası için SearchResultsPage
 */
function seo_json_ld_search() {
    $search = array(
        '@context' => 'https://schema.org',
        '@type' => 'SearchResultsPage',
        'name' => _LANG('title_search') . ' - ' . ayar_getir('site_baslik'),
    );
    return '<script type="application/ld+json">' . json_encode($search, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Anasayfa için WebPage
 */
function seo_json_ld_home() {
    $home = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => ayar_getir('site_baslik'),
        'description' => site_aciklama_getir(),
        'url' => site_url(),
    );
    return '<script type="application/ld+json">' . json_encode($home, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * BreadcrumbList structured data
 */
function seo_json_ld_breadcrumb() {
    $crumbs = seo_get_breadcrumbs();
    if (empty($crumbs)) return '';
    
    $items = array();
    $position = 1;
    foreach ($crumbs as $crumb) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $crumb['name'],
            'item' => $crumb['url'],
        );
    }
    
    $breadcrumb = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    );
    
    return '<script type="application/ld+json">' . json_encode($breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Breadcrumb verilerini üretir
 */
function seo_get_breadcrumbs() {
    $crumbs = array();
    $site_url = site_url();
    
    // Home
    $crumbs[] = array(
        'name' => _LANG('p_menu_anasayfa'),
        'url' => site_url(),
    );
    
    $page = seo_get_page_type();
    
    switch (seo_get_page_type()['type']) {
        case 'ad':
            $id = seo_get_page_type()['id'];
            $tip = seo_get_page_type()['tip'];
            $kategori_id = bilgi_getir('icerik', 'kategori', $id);
            if ($kategori_id) {
                $kategori_adi = _DIL_BASLIK(kategori_adi_getir($kategori_id), $kategori_id, 'kategori');
                $crumbs[] = array(
                    'name' => _DIL_BASLIK(kategori_adi_getir($kategori_id), $kategori_id, 'kategori'),
                    'url' => kategori_link_getir($kategori_id),
                );
            }
            $baslik = _DIL_BASLIK(bilgi_getir('icerik', 'baslik', seo_get_page_type()['id']), seo_get_page_type()['id'], seo_get_page_type()['tip']);
            $crumbs[] = array('name' => mb_substr($baslik, 0, 80), 'url' => '');
            break;
            
        case 'category':
            $id = @$_GET["id"];
            // Alt kategoriler için üst kategorileri bul
            $ust = bilgi_getir('kategoriler', 'ust_kategori', $id);
            while ($ust) {
                $ust_adi = _DIL_BASLIK(kategori_adi_getir($ust), $ust, 'kategori');
                $crumbs[] = array(
                    'name' => $ust_adi,
                    'url' => kategori_link_getir($ust),
                );
                $ust = bilgi_getir('kategoriler', 'ust_kategori', $ust);
            }
            $crumbs = array_reverse($crumbs);
            $crumbs[] = array('name' => _DIL_BASLIK(kategori_adi_getir($id), $id, 'kategori'), 'url' => '');
            break;
            
        case 'store':
            $crumbs[] = array(
                'name' => _LANG('title_all_stores'),
                'url' => site_url() . '/sayfa/magazalar/',
            );
            break;
            
        case 'search':
            $crumbs[] = array('name' => _LANG('title_search'), 'url' => '');
            break;
            
        case 'page':
            $slug = @$_GET["p"];
            $icerik_id = id_getir("icerik", "url", $slug);
            if ($icerik_id) {
                $baslik = _DIL_BASLIK(bilgi_getir("icerik", "baslik", $icerik_id), $icerik_id, "sayfa");
                $crumbs[] = array('name' => $baslik, 'url' => '');
            }
            break;
    }
    
    return $crumbs;
}

/**
 * Hreflang linklerini üretir
 */
function seo_hreflang_tags() {
    $dil = aktif_dil();
    $diller = array('tr', 'en', 'fr', 'ar');
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    // Temiz URL (dil parametresi olmadan)
    $base_url = preg_replace('/[?&]dil=[a-z]{2}/', '', $current_url);
    $base_url = rtrim($base_url, '?&');
    
    $tags = '';
    foreach ($diller as $d) {
        $href = $base_url . (strpos($base_url, '?') === false ? '?' : '&') . 'dil=' . $d;
        $tags .= '<link rel="alternate" hreflang="' . $d . '" href="' . e($href) . '" />' . "\n";
    }
    // x-default
    $tags .= '<link rel="alternate" hreflang="x-default" href="' . e($base_url) . '" />' . "\n";
    
    return $tags;
}

/**
 * Pagination rel=next/prev linkleri
 */
function seo_pagination_links() {
    $php_self = $_SERVER["PHP_SELF"];
    $sayfa = @$_GET["sayfa"] ? (int)@$_GET["sayfa"] : 1;
    
    if (!in_array($php_self, array('kategori.php', 'icerik.php', 'ara.php', 'profil.php', 'magaza.php'))) {
        return '';
    }
    
    $base_url = '';
    $params = $_GET;
    unset($params['sayfa']);
    
    if (strstr($_SERVER["PHP_SELF"], 'kategori.php')) {
        $id = @$_GET["id"];
        $base_url = kategori_link_getir(@$_GET["id"]);
    } elseif (strstr($_SERVER["PHP_SELF"], 'ara.php')) {
        $base_url = site_url() . '/ara/' . urlencode(@$_GET["s"]);
    } elseif (strstr($_SERVER["PHP_SELF"], 'icerik.php')) {
        return ''; // İlan detaylarda sayfalama yok
    } elseif (strstr($_SERVER["PHP_SELF"], 'profil.php')) {
        $base_url = uye_link_getir(@$_GET["id"]);
    } elseif (strstr($_SERVER["PHP_SELF"], 'magaza.php')) {
        $base_url = magaza_link_getir(@$_GET["id"]);
    }
    
    if (!$base_url) return '';
    
    $query = http_build_query($params);
    $base_url .= ($query ? '?' . $query : '');
    
    // Toplam sayfa sayısını bul (basit yaklaşım)
    // Gerçek implementasyonda toplam kayıt sayısı / sayfa başına kayıt hesaplanmalı
    $total_pages = 1; // Placeholder
    
    if ($sayfa > 1) {
        $prev_params = $params;
        $prev_params['sayfa'] = $sayfa - 1;
        $prev_url = $base_url . '&' . http_build_query($prev_params);
        echo '<link rel="prev" href="' . e($prev_url) . '" />' . "\n";
    }
    // Next link için toplam sayfa gerekli
    // if ($sayfa < $total_pages) { ... }
    
    return '';
}

/**
 * Responsive images için srcset yardımcıları
 */
function seo_responsive_image_srcset($image_path, $sizes = array(320, 640, 960, 1200, 1600)) {
    if (!$image_path) return '';
    
    $srcset = array();
    foreach ($sizes as $w) {
        $srcset[] = thumbnail_url($w . '_' . (int)($w * 0.75), $image_path) . ' ' . $w . 'w';
    }
    return implode(', ', $srcset);
}

/**
 * SEO başlık ve açıklama uzunluk kontrolü
 */
function seo_title_check($title) {
    $len = mb_strlen($title);
    if ($len > 60) {
        error_log('[SEO] Title too long (' . $len . ' chars): ' . $title);
    }
    if ($len < 30) {
        error_log('[SEO] Title too short (' . $len . ' chars): ' . $title);
    }
    return $title;
}

function seo_description_check($desc) {
    $len = mb_strlen($desc);
    if ($len > 160) {
        error_log('[SEO] Description too long (' . $len . ' chars): ' . $desc);
    }
    if ($len < 70) {
        error_log('[SEO] Description too short (' . $len . ' chars): ' . $desc);
    }
    return $desc;
}