ALTER TABLE tranches
  ADD COLUMN montant_min NUMERIC,
  ADD COLUMN montant_max NUMERIC;

ALTER TABLE tranches
  DROP COLUMN IF EXISTS limite_superieure;
