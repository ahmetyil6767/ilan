<?php
/**
 * Comprehensive Language System Tests
 * Tests all language and currency functionality
 */

require_once __DIR__ . '/../loader.php';

class LanguageSystemTest {
    private $passed = 0;
    private $failed = 0;
    private $errors = [];

    public function runAllTests() {
        echo "========================================\n";
        echo "LANGUAGE SYSTEM TEST SUITE\n";
        echo "========================================\n\n";

        $this->testLanguageDetection();
        $this->testCurrencyDetection();
        $this->testLanguageSwitchingLinks();
        $this->testCurrencySwitchingLinks();
        $this->testHtmlLangDirAttributes();
        $this->testTranslationDictionary();
        $this->testTranslationFunction();
        $this->testCurrencyFormatting();
        $this->testMonthNames();
        $this->testRtlDetection();
        $this->testCookieSessionPersistence();
        $this->testEdgeCases();
        $this->testLanguageFileConsistency();
        $this->testCriticalUiStrings();

        $this->printSummary();
    }

    private function assert($condition, $message) {
        if ($condition) {
            $this->passed++;
            echo "  ✓ $message\n";
        } else {
            $this->failed++;
            $this->errors[] = $message;
            echo "  ✗ FAIL: $message\n";
        }
    }

    private function resetState() {
        $_SESSION = [];
        $_COOKIE = [];
        $_GET = [];
        $GLOBALS['_aktif_dil'] = null;
        $GLOBALS['_aktif_birim'] = null;
    }

    // ==================== TEST METHODS ====================

    private function testLanguageDetection() {
        echo "\n--- testLanguageDetection ---\n";
        
        foreach (['tr', 'en', 'fr', 'ar'] as $dil) {
            $this->resetState();
            $_GET['dil'] = $dil;
            $result = aktif_dil();
            $this->assert($result === $dil, "?dil=$dil returns '$dil'");
        }

        $this->resetState();
        $result = aktif_dil();
        $this->assert($result === 'en', "Default returns 'en'");

        // Test invalid language falls back to default
        $this->resetState();
        $_GET['dil'] = 'xx';
        $result = aktif_dil();
        $this->assert($result === 'en', "Invalid lang 'xx' falls back to 'en'");
    }

    private function testCurrencyDetection() {
        echo "\n--- testCurrencyDetection ---\n";
        
        foreach (['gmd', 'usd', 'eur', 'gbp'] as $birim) {
            $this->resetState();
            $_GET['birim'] = $birim;
            $result = aktif_birim();
            $this->assert($result === $birim, "?birim=$birim returns '$birim'");
        }

        $this->resetState();
        $result = aktif_birim();
        $this->assert($result === 'gmd', "Default returns 'gmd'");

        // Test invalid currency falls back to default
        $this->resetState();
        $_GET['birim'] = 'xyz';
        $result = aktif_birim();
        $this->assert($result === 'gmd', "Invalid currency 'xyz' falls back to 'gmd'");
    }

    private function testLanguageSwitchingLinks() {
        echo "\n--- testLanguageSwitchingLinks ---\n";
        
        $originalUri = $_SERVER['REQUEST_URI'];
        $_SERVER['REQUEST_URI'] = '/test?foo=bar';

        foreach (['tr', 'en', 'fr', 'ar'] as $dil) {
            $link = _DIL_DEGISTIR_LINK($dil);
            $this->assert(
                strpos($link, "dil=$dil") !== false,
                "$dil link contains 'dil=$dil'"
            );
            $this->assert(
                strpos($link, 'foo=bar') !== false,
                "$dil link preserves existing query params"
            );
        }

        $_SERVER['REQUEST_URI'] = $originalUri;
    }

    private function testCurrencySwitchingLinks() {
        echo "\n--- testCurrencySwitchingLinks ---\n";
        
        $originalUri = $_SERVER['REQUEST_URI'];
        $_SERVER['REQUEST_URI'] = '/test?foo=bar';

        foreach (['gmd', 'usd', 'eur', 'gbp'] as $birim) {
            $link = _BIRIM_DEGISTIR_LINK($birim);
            $this->assert(
                strpos($link, "birim=$birim") !== false,
                "$birim link contains 'birim=$birim'"
            );
            $this->assert(
                strpos($link, 'foo=bar') !== false,
                "$birim link preserves existing query params"
            );
        }

        $_SERVER['REQUEST_URI'] = $originalUri;
    }

    private function testHtmlLangDirAttributes() {
        echo "\n--- testHtmlLangDirAttributes ---\n";
        
        foreach (['tr', 'en', 'fr'] as $dil) {
            $GLOBALS['_aktif_dil'] = $dil;
            $lang = _DIL_HTML_LANG();
            $dir = _DIL_HTML_YON();
            $rtl = _DIL_RTL_MI();
            
            $this->assert($lang === $dil, "$dil: lang='$lang'");
            $this->assert($dir === 'ltr', "$dil: dir='ltr'");
            $this->assert($rtl === false, "$dil: rtl=false");
        }

        $GLOBALS['_aktif_dil'] = 'ar';
        $lang = _DIL_HTML_LANG();
        $dir = _DIL_HTML_YON();
        $rtl = _DIL_RTL_MI();
        
        $this->assert($lang === 'ar', "ar: lang='ar'");
        $this->assert($dir === 'rtl', "ar: dir='rtl'");
        $this->assert($rtl === true, "ar: rtl=true");
        
        unset($GLOBALS['_aktif_dil']);
    }

    private function testTranslationDictionary() {
        echo "\n--- testTranslationDictionary ---\n";
        
        foreach (['tr', 'en', 'fr', 'ar'] as $dil) {
            $this->resetState();
            $_GET['dil'] = $dil;
            $_SESSION['dil'] = $dil;
            $GLOBALS['_aktif_dil'] = $dil;
            
            $sozluk = _DIL_SOZLUK();
            $this->assert(
                count($sozluk) > 3000,
                "$dil: dictionary has >3000 keys (" . count($sozluk) . ")"
            );
        }
    }

    private function testTranslationFunction() {
        echo "\n--- testTranslationFunction ---\n";
        
        $criticalKeys = [
            'search_placeholder',
            'nav_login',
            'nav_register', 
            'nav_free_post',
            'ilan_taslak_uyari',
            'ilan_onay_kuyrugu',
            'ie_btn_kaydet',
            'ie_btn_yayinla',
            'ie_btn_guncelle',
            'pg_guncelle',
            'gy_panel_giris',
            'ko_kayit_ol',
        ];

        foreach (['tr', 'en', 'fr', 'ar'] as $dil) {
            $this->resetState();
            $_GET['dil'] = $dil;
            $_SESSION['dil'] = $dil;
            $GLOBALS['_aktif_dil'] = $dil;
            
            $sozluk = _DIL_SOZLUK();
            $missing = 0;
            
            foreach ($criticalKeys as $key) {
                $result = _LANG($key);
                $isMissing = ($result === $key);
                if ($isMissing) $missing++;
            }
            
            $this->assert(
                $missing === 0,
                "$dil: all " . count($criticalKeys) . " critical keys translated"
            );
        }
    }

    private function testCurrencyFormatting() {
        echo "\n--- testCurrencyFormatting ---\n";
        
        $testAmounts = ['50000', '1500000', '25000,50'];
        
        foreach (['gmd', 'usd', 'eur', 'gbp'] as $birim) {
            foreach ($testAmounts as $amount) {
                $result = birim_goster($amount, $birim);
                $this->assert(
                    $result !== '' && strpos($result, strtoupper($birim)) !== false,
                    "$amount GMD -> $birim: formatted with currency code"
                );
            }
        }
    }

    private function testMonthNames() {
        echo "\n--- testMonthNames ---\n";
        
        foreach (['tr', 'en', 'fr', 'ar'] as $dil) {
            $this->resetState();
            $_GET['dil'] = $dil;
            $_SESSION['dil'] = $dil;
            $GLOBALS['_aktif_dil'] = $dil;
            
            $months = [];
            for ($m = 1; $m <= 12; $m++) {
                $months[] = _DIL_AY_AY($m);
            }
            
            $allNonNumeric = true;
            foreach ($months as $month) {
                if (is_numeric($month)) {
                    $allNonNumeric = false;
                    break;
                }
            }
            
            $this->assert(
                $allNonNumeric && count($months) === 12,
                "$dil: 12 month names returned (non-numeric)"
            );
        }
    }

    private function testRtlDetection() {
        echo "\n--- testRtlDetection ---\n";
        
        // Clear any cached state first
        $this->resetState();
        unset($GLOBALS['_aktif_dil']);
        unset($GLOBALS['_aktif_birim']);
        
        $this->assert(_DIL_RTL_MI() === false, "Default (en) is not RTL");
        
        $GLOBALS['_aktif_dil'] = 'ar';
        $this->assert(_DIL_RTL_MI() === true, "ar is RTL");
        
        $GLOBALS['_aktif_dil'] = 'tr';
        $this->assert(_DIL_RTL_MI() === false, "tr is not RTL");
        
        unset($GLOBALS['_aktif_dil']);
    }

    private function testCookieSessionPersistence() {
        echo "\n--- testCookieSessionPersistence ---\n";
        
        // Test cookie persistence
        $this->resetState();
        $_COOKIE['dil'] = 'fr';
        $result = aktif_dil();
        $this->assert($result === 'fr', "Cookie 'fr' respected");

        // Test session persistence (only when no cookie)  
        $this->resetState();
        $_SESSION['dil'] = 'ar';
        // Need to ensure no cookie is set for session to work
        unset($_COOKIE['dil']);
        $result = aktif_dil();
        $this->assert($result === 'ar', "Session 'ar' respected (no cookie)");

        // Test cookie overrides session (cookie checked before session)
        $this->resetState();
        $_COOKIE['dil'] = 'tr';
        $_SESSION['dil'] = 'ar';
        $result = aktif_dil();
        $this->assert($result === 'tr', "Cookie 'tr' overrides Session 'ar'");

        // Test currency cookie
        $this->resetState();
        $_COOKIE['birim'] = 'eur';
        $result = aktif_birim();
        $this->assert($result === 'eur', "Currency cookie 'eur' respected");
    }

    private function testEdgeCases() {
        echo "\n--- testEdgeCases ---\n";
        
        $this->resetState();
        $_GET['dil'] = 'en';
        $_SESSION['dil'] = 'en';
        $GLOBALS['_aktif_dil'] = 'en';
        
        // Empty key
        $result = _LANG('');
        $this->assert($result === '', "Empty key returns empty string");
        
        // Non-existent key
        $result = _LANG('nonexistent_key_12345');
        $this->assert(
            $result === 'nonexistent_key_12345',
            "Non-existent key returns itself"
        );
        
        // Key with parameters (f_saniye_once has %s placeholder)
        $result = _LANG('f_saniye_once', ['5']);
        $this->assert(
            strpos($result, '5') !== false,
            "Key with parameters formats correctly (f_saniye_once)"
        );
    }

    private function testLanguageFileConsistency() {
        echo "\n--- testLanguageFileConsistency ---\n";
        
        $keys = [];
        foreach (['tr', 'en', 'fr', 'ar'] as $dil) {
            $content = file_get_contents(__DIR__ . "/../lang/$dil.php");
            preg_match('/return\s+array\s*\(([\s\S]*)\)/', $content, $matches);
            $fileKeys = [];
            preg_match_all("/'([^']+)'\s*=>/", $matches[1], $matches);
            $fileKeys = array_flip($matches[1]);
            $keys[$dil] = $fileKeys;
        }

        foreach (['fr', 'ar', 'tr'] as $dil) {
            $missing = array_diff(array_keys($keys['en']), array_keys($keys[$dil]));
            $this->assert(
                count($missing) === 0,
                "$dil has all EN keys (missing: " . count($missing) . ")"
            );
        }
    }

    private function testCriticalUiStrings() {
        echo "\n--- testCriticalUiStrings ---\n";
        
        $criticalStrings = [
            'search_placeholder',
            'nav_login',
            'nav_register',
            'nav_logout',
            'nav_free_post',
            'nav_my_profile',
            'nav_my_messages',
            'nav_my_favorites',
            'nav_my_ads',
            'nav_my_stores',
            'nav_create_store',
            'nav_my_orders',
            'nav_sales',
            'nav_update_profile',
            'nav_admin_panel',
        ];

        foreach (['tr', 'en', 'fr', 'ar'] as $dil) {
            $this->resetState();
            $_GET['dil'] = $dil;
            $_SESSION['dil'] = $dil;
            $GLOBALS['_aktif_dil'] = $dil;
            
            $missing = 0;
            foreach ($criticalStrings as $key) {
                $result = _LANG($key);
                if ($result === $key) $missing++;
            }
            
            $this->assert(
                $missing === 0,
                "$dil: all " . count($criticalStrings) . " nav/UI strings translated"
            );
        }
    }

    private function printSummary() {
        echo "\n========================================\n";
        echo "TEST SUMMARY\n";
        echo "========================================\n";
        echo "Passed: $this->passed\n";
        echo "Failed: $this->failed\n";
        echo "Total:  " . ($this->passed + $this->failed) . "\n";
        
        if ($this->failed > 0) {
            echo "\nFAILURES:\n";
            foreach ($this->errors as $error) {
                echo "  - $error\n";
            }
            exit(1);
        } else {
            echo "\n✓ ALL TESTS PASSED\n";
            exit(0);
        }
    }
}

// Run tests
$test = new LanguageSystemTest();
$test->runAllTests();