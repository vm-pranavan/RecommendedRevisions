<?php
/**
 * LocalSettings.php for CI testing of RecommendedRevisions.
 *
 * This file is auto-mounted into the MediaWiki container by docker-compose.
 * Extension/skin load lines are appended at the bottom by install_extensions.sh.
 */

# ── Core settings ────────────────────────────────────────────────────────────

$wgSitename = "RecommendedRevisions CI";
$wgServer = getenv('MW_SERVER') ?: "http://localhost:8080";
$wgScriptPath = getenv('MW_SCRIPT_PATH') !== false ? getenv('MW_SCRIPT_PATH') : "";

$wgArticlePath = "$wgScriptPath/$1";

# ── Database ─────────────────────────────────────────────────────────────────

$wgDBtype = getenv('MW_DB_TYPE') ?: "mysql";
$wgDBserver = getenv('MW_DB_SERVER') ?: "db";
$wgDBname = getenv('MW_DB_NAME') ?: "mediawiki";
$wgDBuser = getenv('MW_DB_USER') ?: "wiki";
$wgDBpassword = getenv('MW_DB_PASS') ?: "wiki_password";
$wgDBprefix = "";
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

# ── Paths / uploads ─────────────────────────────────────────────────────────

$wgEnableUploads = true;
$wgUploadDirectory = "{$IP}/images";
$wgUseImageMagick = false;

# ── Development / debugging ──────────────────────────────────────────────────

$wgShowExceptionDetails = true;
$wgShowDBErrorBacktrace = true;
$wgDevelopmentWarnings = true;
$wgDeprecationReleaseLimit = false;

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

# ── Secrets (throwaway CI values) ────────────────────────────────────────────

$wgSecretKey = "ci-secret-key-not-for-production-use-0123456789abcdef0123456789abcdef";
$wgUpgradeKey = "ci-upgrade-key";

# ── Performance ──────────────────────────────────────────────────────────────

$wgMainCacheType = CACHE_NONE;
$wgCacheDirectory = false;
$wgObjectCaches[CACHE_DB] = [ 'class' => SqlBagOStuff::class ];
$wgJobRunRate = 0;

# ── Permissions (allow install script to run update.php) ─────────────────────

$wgGroupPermissions['*']['read'] = true;
$wgGroupPermissions['*']['edit'] = false;

# ── Extensions & skins ──────────────────────────────────────────────────────
# The lines below are auto-appended by .ci/install_extensions.sh.
# Do not manually add wfLoadExtension / wfLoadSkin calls above this line.

# --- BEGIN AUTO-GENERATED EXTENSION LOADS ---
