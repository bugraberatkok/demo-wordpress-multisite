<?php
/**
 * MCP sunucusu kaydi (resmi WordPress MCP Adapter eklentisi uzerinden).
 *
 * Adapter, WordPress'in butun islemlerini kendiliginden acmaz: hangi
 * Abilities'in arac (tool) olarak sunulacagini burada tek tek sayiyoruz.
 * Bu demoda yalnizca kendi tanimladigimiz dort yetenek disari acilir;
 * cekirdegin kendi yetenekleri (core/get-site-info vb.) dahil edilmez.
 *
 * Uc nokta: /wp-json/nwcs/v1/mcp   (ag ana sitesi uzerinden)
 */

defined( 'ABSPATH' ) || exit;

add_action( 'mcp_adapter_init', 'nwcs_register_mcp_server' );
function nwcs_register_mcp_server( $adapter ): void {
	if ( ! is_object( $adapter ) || ! method_exists( $adapter, 'create_server' ) ) {
		return;
	}

	$adapter->create_server(
		'nwcs-studio',
		'nwcs/v1',
		'mcp',
		'İçerik Stüdyosu',
		'Yerel demo sitelerinin içerik alanlarını okur ve kontrollü şekilde günceller.',
		'v0.3.0',
		array( \WP\MCP\Transport\HttpTransport::class ),
		\WP\MCP\Infrastructure\ErrorHandling\ErrorLogMcpErrorHandler::class,
		\WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class,
		array(
			'nwcs/list-sites',
			'nwcs/describe-site',
			'nwcs/get-field',
			'nwcs/update-field',
		),
		array(),
		array()
	);
}
