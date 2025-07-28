INSERT INTO clients (nom, prenom) VALUES
  ('Khouss', 'Ngom'),
  ('Ndiaye', 'Moustapha'),
  ('Senghor', 'Fallou');

INSERT INTO compteurs (numero_compteur, client_id) VALUES
  ('1000', 1),
  ('1111', 2),
  ('2222', 3);

INSERT INTO tranches (libelle, montant_min, montant_max, prix_unitaire) VALUES
  ('Tranche 1', 1000, 20000, 100.00),
  ('Tranche 2', 20001, 50000, 200.00),
  ('Tranche 3', 50001, 99999999, 300.00);

INSERT INTO achats (reference, code_recharge, date_achat, heure_achat, montant, nbre_kwt, tranche, prix_kw, statut, ip, localisation, numero_compteur) VALUES
  ('WOY-202507270001', 'ABCD1234', '2025-07-27', '10:00:00', 1000.00, 10.00, 'Tranche 1', 100.00, 'success', '127.0.0.1', 'Dakar', '1000'),
  ('WOY-202507270002', 'EFGH5678', '2025-07-27', '11:00:00', 2000.00, 10.00, 'Tranche 2', 200.00, 'success', '127.0.0.1', 'Dakar', '1111'),
  ('WOY-202507270003', 'IJKL9012', '2025-07-27', '12:00:00', 3000.00, 10.00, 'Tranche 3', 300.00, 'success', '127.0.0.1', 'Dakar', '2222');
