<?php
/**
 * Mandatory Security
 */
require __DIR__ . '/scripts/xmlrpc.php';
require __DIR__ . '/scripts/pingback.php';
require __DIR__ . '/scripts/feeds.php';
require __DIR__ . '/scripts/removeMetaTags.php'; // fingerprint
require __DIR__ . '/scripts/restApi.php';
require __DIR__ . '/scripts/securityHeaders.php';

/**
 * Highly Recommended
 */
require __DIR__ . '/scripts/staticGetVersions.php'; // fingerprint
require __DIR__ . '/scripts/allowSvg.php';

/**
 * Remove Console Trace
 * Recommended for Better Google Insights Score
 * Sometimes needed for backward compatibility
 */
require __DIR__ . '/scripts/removeJqueryMigrate.php';
