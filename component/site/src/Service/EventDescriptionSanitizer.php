<?php
namespace Xdecaro\Component\Decaroevents\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Filter\InputFilter;

final class EventDescriptionSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's',
        'ul', 'ol', 'li', 'blockquote',
        'h2', 'h3', 'h4', 'h5', 'h6',
        'a', 'span', 'div', 'pre', 'code',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
    ];

    private const ALLOWED_ATTRIBUTES = [
        'href', 'title', 'class', 'colspan', 'rowspan',
    ];

    public static function sanitize(string $html): string
    {
        if ($html === '') {
            return '';
        }

        $filter = InputFilter::getInstance(
            self::ALLOWED_TAGS,
            self::ALLOWED_ATTRIBUTES,
            InputFilter::ONLY_ALLOW_DEFINED_TAGS,
            InputFilter::ONLY_ALLOW_DEFINED_ATTRIBUTES,
            1
        );

        return (string) $filter->clean($html, 'html');
    }
}
