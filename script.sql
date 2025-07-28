CREATE TABLE clients (
  id_client SERIAL PRIMARY KEY,
  nom VARCHAR(100),
  prenom VARCHAR(100)
);

CREATE TABLE compteurs (
  numero_compteur VARCHAR(50) PRIMARY KEY,
  client_id INTEGER REFERENCES clients(id_client) ON DELETE CASCADE
);

CREATE TABLE tranches (
  id_tranche SERIAL PRIMARY KEY,
  libelle VARCHAR(100),
  prix_unitaire NUMERIC(10,2),
  limite_superieure NUMERIC(10,2)
);

CREATE TABLE achats (
  id_achat SERIAL PRIMARY KEY,
  reference VARCHAR(100) UNIQUE,
  code_recharge VARCHAR(100),
  date_achat DATE,
  heure_achat TIME,
  montant NUMERIC(10,2),
  nbre_kwt NUMERIC(10,2),
  tranche VARCHAR(100),
  prix_kw NUMERIC(10,2),
  statut VARCHAR(50),
  ip VARCHAR(100),
  localisation VARCHAR(100),
  numero_compteur VARCHAR(50) REFERENCES compteurs(numero_compteur) ON DELETE CASCADE
);
