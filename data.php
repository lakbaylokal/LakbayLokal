<?php
// data.php — LakbayLokal destination data
// Include this file wherever destination data is needed.

$destinations = [
  [
    'id' => 'baguio', 'name' => 'Baguio City', 'region' => 'luzon', 'price' => 4500,
    'emoji' => '🏔️',
    'gradient' => 'linear-gradient(135deg, #6DB1C5, #2E6B4F)',
    'tagline' => 'The Summer Capital of the Philippines',
    'desc' => 'Escape to the cool mountain air of Baguio City and discover pine forests, strawberry farms, and rich Igorot culture.',
    'activities' => ['Strawberry Farms', 'Adventure Park', 'Museums'],
    'hotels' => [
      ['name' => 'Sotogrande Hotel Baguio',         'url' => 'https://www.sotograndehotels.com/our-hotels/sotogrande-baguio/'],
      ['name' => 'Microtel by Wyndham Baguio',      'url' => 'https://www.wyndhamhotels.com/microtel/baguio-philippines/microtel-by-wyndham-baguio/overview'],
      ['name' => 'Travelite Express Hotel',         'url' => 'https://www.traveloka.com/en-ph/hotel/philippines/travelite-express-hotel-3000020013897'],
    ],
    'acts' => [
      ['name' => 'Strawberry Picking at La Trinidad Farm', 'price' => 250],
      ['name' => 'BenCab Museum Gallery Tour',             'price' => 200],
      ['name' => 'Tree Top Adventure – Camp John Hay',     'price' => 400],
      ['name' => 'Igorot Stone Kingdom Exploration',       'price' => 150],
    ],
  ],
  [
    'id' => 'vigan', 'name' => 'Vigan City', 'region' => 'luzon', 'price' => 6500,
    'emoji' => '🏛️',
    'gradient' => 'linear-gradient(135deg, #C4A882, #6B4226)',
    'tagline' => 'UNESCO World Heritage City',
    'desc' => 'Walk the cobblestone streets of a colonial-era city frozen in time, with kalesas, ancestral houses, and Ilocano cuisine.',
    'activities' => ['Heritage Walk', 'Pottery', 'Calesa Ride'],
    'hotels' => [
      ['name' => 'Hotel Felicidad Vigan', 'url' => 'https://www.hotelfelicidadvigan.com/'],
      ['name' => 'Paradores de Vigan',   'url' => 'https://www.paradoresdevigan.com/'],
      ['name' => 'Hotel Luna',           'url' => 'https://www.hotelluna.ph/'],
    ],
    'acts' => [
      ['name' => 'Calesa Ride around Calle Crisologo',      'price' => 250],
      ['name' => 'Pagburnayan Jar Factory Pottery Making',  'price' => 300],
      ['name' => 'Vigan Museum / Syquia Mansion Tour',      'price' => 180],
    ],
  ],
  [
    'id' => 'palawan', 'name' => 'Palawan', 'region' => 'luzon', 'price' => 8500,
    'emoji' => '🏝️',
    'gradient' => 'linear-gradient(135deg, #43BEAC, #0B5E6E)',
    'tagline' => 'Last Frontier of the Philippines',
    'desc' => 'Crystal lagoons, secret beaches, WWII shipwrecks, and the world-famous Underground River await in paradise Palawan.',
    'activities' => ['Lagoon Tours', 'Underground River', 'Diving'],
    'hotels' => [
      ['name' => 'Seda Lio – El Nido',               'url' => 'https://sedahotels.com/sedalio/'],
      ['name' => 'Hue Hotels – Puerto Princesa',     'url' => 'https://thehuehotel.com/puertoprincesa/'],
      ['name' => 'Two Seasons Coron Island Resort',  'url' => 'https://twoseasonsresorts.com/coron/'],
    ],
    'acts' => [
      ['name' => 'El Nido Tour A – Lagoons & Islands',       'price' => 1200],
      ['name' => 'Puerto Princesa Underground River Tour',   'price' => 2750],
      ['name' => 'Coron Shipwreck & Snorkeling Tour',        'price' => 1600],
      ['name' => 'Wildlife Safari at Calauit Sanctuary',     'price' => 2500],
    ],
  ],
  [
    'id' => 'cebu', 'name' => 'Cebu City', 'region' => 'visayas', 'price' => 5500,
    'emoji' => '🌊',
    'gradient' => 'linear-gradient(135deg, #5DC8E0, #1A6E8A)',
    'tagline' => 'The Queen City of the South',
    'desc' => 'From canyoneering in Kawasan Falls to swimming with whale sharks in Oslob — Cebu offers it all.',
    'activities' => ['Canyoneering', 'Whale Sharks', 'Temple'],
    'hotels' => [
      ['name' => 'Quest Hotel Cebu',    'url' => 'https://www.questhotelsandresorts.com/cebu'],
      ['name' => 'Radisson Blu Cebu',  'url' => 'https://www.radissonhotels.com/en-us/hotels/radisson-blu-cebu'],
      ['name' => 'Bayfront Hotel Cebu','url' => 'https://www.bayfronthotelcebu.com'],
    ],
    'acts' => [
      ['name' => 'Kawasan Falls Canyoneering',   'price' => 1500],
      ['name' => 'Temple of Leah Tour',          'price' => 100],
      ['name' => 'Oslob Whale Shark Watching',   'price' => 500],
    ],
  ],
  [
    'id' => 'boracay', 'name' => 'Boracay Island', 'region' => 'visayas', 'price' => 7500,
    'emoji' => '🏖️',
    'gradient' => 'linear-gradient(135deg, #F0CE79, #E06B30)',
    'tagline' => 'World-Famous White Sand Beach',
    'desc' => "Powdery white sand, turquoise waters, and endless island adventures — Boracay is the Philippines' crown jewel.",
    'activities' => ['Island Hopping', 'Parasailing', 'Diving'],
    'hotels' => [
      ['name' => 'Henann Crystal Sands Resort',      'url' => 'https://www.henann.com/henanncrystalsands/'],
      ['name' => 'Fairways and Bluewater Boracay',   'url' => 'https://fairwaysandbluewater.com/'],
      ['name' => 'La Carmela de Boracay Resort Hotel','url' => 'https://www.guestreservations.com/la-carmela-de-boracay-resort-hotel/booking'],
    ],
    'acts' => [
      ['name' => 'Island Hopping Tour',  'price' => 800],
      ['name' => 'Parasailing Activity', 'price' => 2000],
      ['name' => 'Helmet Diving',        'price' => 700],
    ],
  ],
  [
    'id' => 'siargao', 'name' => 'Siargao Island', 'region' => 'mindanao', 'price' => 3700,
    'emoji' => '🏄',
    'gradient' => 'linear-gradient(135deg, #56C8A0, #1A5E4A)',
    'tagline' => 'Surfing Capital of the Philippines',
    'desc' => "Ride the world-famous Cloud 9 waves, hop between pristine islands, and soak in Siargao's laid-back surf culture.",
    'activities' => ['Surfing', 'Island Hopping', 'Bike Rides'],
    'hotels' => [
      ['name' => 'View available hotels in Siargao', 'url' => 'https://www.booking.com/searchresults.en-gb.html?ss=Siargao'],
    ],
    'acts' => [
      ['name' => 'Island Hopping',            'price' => 2500],
      ['name' => 'Basic Surf Lesson',          'price' => 700],
      ['name' => 'Motorbike Rental (per day)', 'price' => 500],
    ],
  ],
  [
    'id' => 'bukidnon', 'name' => 'Bukidnon', 'region' => 'mindanao', 'price' => 2100,
    'emoji' => '🏕️',
    'gradient' => 'linear-gradient(135deg, #94C96A, #3A6B1A)',
    'tagline' => 'Thrills at Dahilayan Adventure Park',
    'desc' => "Experience the longest zipline in Asia, ATV rides through highland meadows, and the cool breeze of Mindanao's highlands.",
    'activities' => ['Zipline', 'ATV', 'DropZone'],
    'hotels' => [
      ['name' => 'View available hotels in Bukidnon', 'url' => 'https://www.booking.com/searchresults.en-gb.html?ss=Bukidnon'],
    ],
    'acts' => [
      ['name' => 'ATV – Dahilayan Adventure Park',       'price' => 850],
      ['name' => '840m Zipline – Dahilayan Adventure Park','price' => 500],
      ['name' => 'DropZone – Dahilayan Adventure Park',  'price' => 500],
      ['name' => 'ZipKart – Dahilayan Adventure Park',   'price' => 250],
    ],
  ],
  [
    'id' => 'camiguin', 'name' => 'Camiguin Island', 'region' => 'mindanao', 'price' => 7500,
    'emoji' => '🌋',
    'gradient' => 'linear-gradient(135deg, #6EC9B5, #1D5E50)',
    'tagline' => 'Island Born of Fire',
    'desc' => 'This tiny island has more volcanoes per square kilometer than anywhere on Earth, plus stunning waterfalls and turquoise springs.',
    'activities' => ['Island Hopping', 'Waterfalls', 'Diving'],
    'hotels' => [
      ['name' => 'View available hotels in Camiguin', 'url' => 'https://www.booking.com/searchresults.en-gb.html?ss=Camiguin'],
    ],
    'acts' => [
      ['name' => 'Island Hopping',  'price' => 2500],
      ['name' => 'Waterfalls Tour', 'price' => 3500],
      ['name' => 'Scuba Diving',    'price' => 2150],
    ],
  ],
];
