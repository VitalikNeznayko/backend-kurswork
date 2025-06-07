<?php
function sortLink(string $label, string $sortValue, array $currentGet, string $Path, array $excludeKeys = ['route'], string $defaultSort = 'date_desc'): string
{
    $params = array_filter(
        $currentGet,
        fn($key) => !in_array($key, $excludeKeys, true),
        ARRAY_FILTER_USE_KEY
    );

    $params['sort'] = $sortValue;
    $queryString = http_build_query($params);

    $url = $Path . $queryString;

    $isActive = (
        ($currentGet['sort'] ?? null) === $sortValue
        || (!isset($currentGet['sort']) && $sortValue === $defaultSort)
    );

    $active = $isActive ? 'active' : '';

    return "<button type='button' class='btn sort-btn btn-outline-secondary $active' data-sort=\"$sortValue\" data-url=\"$url\">$label</button>";
}

$sort = $_GET['sort'] ?? '';
$currentGet = $_GET;
?>