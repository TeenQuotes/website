<?php
namespace TeenQuotes\Mail\Transport;

use Swift_Transport;
use Swift_Mime_Message;
use GuzzleHttp\Post\PostFile;
use Swift_Events_EventListener;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class MailgunTransport extends \Illuminate\Mail\Transport\MailgunTransport {

	/**
	 * {@inheritdoc}
	 */
	public function send(Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$client = $this->getHttpClient();

		// Inline CSS here
		$converter = new CssToInlineStyles();
		$converter->setEncoding($message->getCharset());
		$converter->setUseInlineStylesBlock();
		$converter->setCleanup();

		if ($message->getContentType() === 'text/html' ||
			($message->getContentType() === 'multipart/alternative' && $message->getBody())
		) {
			$converter->setHTML($message->getBody());
			$message->setBody($converter->convert());
		}

		foreach ($message->getChildren() as $part) {
			if (strpos($part->getContentType(), 'text/html') === 0) {
				$converter->setHTML($part->getBody());
				$part->setBody($converter->convert());
			}
		}

		// Call the API
		$client->post($this->url, ['auth' => ['api', $this->key],
			'body' => [
				'to' => $this->getTo($message),
				'message' => new PostFile('message', (string) $message),
			],
		]);
	}
}