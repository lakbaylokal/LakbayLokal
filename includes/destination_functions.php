function filterDestinations($destinations, $region, $budget) {

  if ($region) {
    $destinations = array_filter($destinations, function($d) use ($region) {
      return strtolower($d['region']) === strtolower($region);
    });
  }

  if ($budget === 'low') {
    $destinations = array_filter($destinations, fn($d) => $d['price'] < 5000);
  }

  if ($budget === 'mid') {
    $destinations = array_filter($destinations, fn($d) => $d['price'] >= 5000 && $d['price'] <= 7500);
  }

  if ($budget === 'high') {
    $destinations = array_filter($destinations, fn($d) => $d['price'] > 7500);
  }

  return $destinations;
}