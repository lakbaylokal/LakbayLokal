-- Repair destination image paths for the numeric-ID LakbayLokal schema.
-- Run after importing database/lakbaylokalsql-try.sql if destination cards show
-- gradients or blank placeholders instead of the bundled images.

UPDATE destinations
SET
  image_url = CASE name
    WHEN 'Baguio City' THEN 'assets/pics/Baguio.jpg'
    WHEN 'Vigan City' THEN 'assets/pics/vigan.jpg'
    WHEN 'Palawan' THEN 'assets/pics/palawan.jpg'
    WHEN 'Cebu City' THEN 'assets/pics/Cebu2.jpg'
    WHEN 'Boracay Island' THEN 'assets/pics/boracay.jpg'
    WHEN 'Siargao Island' THEN 'assets/pics/siargao.jpg'
    WHEN 'Bukidnon' THEN 'assets/pics/bukidno.jpg'
    WHEN 'Camiguin Island' THEN 'assets/pics/camiguin.jpg'
    ELSE image_url
  END,
  gradient_bg = CASE name
    WHEN 'Baguio City' THEN 'linear-gradient(135deg, rgba(109,177,197,0.75), rgba(0,0,0,0.22)), url(''assets/pics/Baguio.jpg'') center/cover no-repeat'
    WHEN 'Vigan City' THEN 'linear-gradient(135deg, rgba(0,0,0,0.34), rgba(0,0,0,0.05)), url(''assets/pics/vigan.jpg'') center/cover no-repeat'
    WHEN 'Palawan' THEN 'linear-gradient(135deg, rgba(0,0,0,0.28), rgba(0,0,0,0.1)), url(''assets/pics/palawan.jpg'') center/cover no-repeat'
    WHEN 'Cebu City' THEN 'linear-gradient(135deg, rgba(0,0,0,0.32), rgba(0,0,0,0.08)), url(''assets/pics/Cebu2.jpg'') center/cover no-repeat'
    WHEN 'Boracay Island' THEN 'linear-gradient(135deg, rgba(0,0,0,0.22), rgba(0,0,0,0.08)), url(''assets/pics/boracay.jpg'') center/cover no-repeat'
    WHEN 'Siargao Island' THEN 'linear-gradient(135deg, rgba(0,0,0,0.32), rgba(0,0,0,0.08)), url(''assets/pics/siargao.jpg'') center/cover no-repeat'
    WHEN 'Bukidnon' THEN 'linear-gradient(135deg, rgba(0,0,0,0.28), rgba(0,0,0,0.06)), url(''assets/pics/bukidno.jpg'') center/cover no-repeat'
    WHEN 'Camiguin Island' THEN 'linear-gradient(135deg, rgba(0,0,0,0.32), rgba(0,0,0,0.08)), url(''assets/pics/camiguin.jpg'') center/cover no-repeat'
    ELSE gradient_bg
  END
WHERE name IN (
  'Baguio City',
  'Vigan City',
  'Palawan',
  'Cebu City',
  'Boracay Island',
  'Siargao Island',
  'Bukidnon',
  'Camiguin Island'
);

-- Optional local repair for the two custom hotel rows whose uploaded filenames
-- were missing from assets/pics but had matching uploaded hotel photos available.
UPDATE hotels
SET image_url = CASE id
  WHEN 25 THEN 'assets/pics/hotel_6a35876e3f8bf.jpg'
  WHEN 26 THEN 'assets/pics/hotel_6a358387aafa8.png'
  ELSE image_url
END
WHERE id IN (25, 26);
