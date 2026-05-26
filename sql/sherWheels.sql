CREATE DATABASE IF NOT EXISTS sherWheels;
USE sherWheels;

CREATE TABLE billeterie (
    id_billet INT AUTO_INCREMENT PRIMARY KEY,
    type_billet VARCHAR(50) NOT NULL,
    prix DECIMAL(10,2) NOT NULL CHECK (prix >= 0),
    places_disponibles INT UNSIGNED NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    token_reset VARCHAR(255) DEFAULT NULL,
    token_expire DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('admin','user') NOT NULL DEFAULT 'user'
);

CREATE TABLE programme (
    id_programme INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    intervenant VARCHAR(100),
    description TEXT,
    date DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    is_published BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE reservation (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    id_billet INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    nombre_place INT NOT NULL DEFAULT 1 CHECK (nombre_place > 0),
    preference ENUM('auto','moto','auto_moto') NOT NULL DEFAULT 'auto_moto',
    deplacement ENUM('voiture','covoiturage','transport') NOT NULL DEFAULT 'voiture',
    status ENUM('confirmee','annulee','en_attente') NOT NULL DEFAULT 'en_attente',
    date_reservation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_billet) REFERENCES billeterie(id_billet)
);

CREATE TABLE contact (
    id_contact INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    sujet VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    date_envoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  description TEXT,
  description_longue TEXT,
  prix DECIMAL(10,2) NOT NULL CHECK (prix >= 0),
  image VARCHAR(255),
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tailles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(10) NOT NULL UNIQUE
);

CREATE TABLE produits_tailles (
  produit_id INT NOT NULL,
  taille_id INT NOT NULL,
  stock INT UNSIGNED DEFAULT 0,
  PRIMARY KEY (produit_id, taille_id),
  FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
  FOREIGN KEY (taille_id) REFERENCES tailles(id) ON DELETE CASCADE
);

CREATE TABLE commandes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NULL,
  email VARCHAR(255) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  stripe_payment_id VARCHAR(255),
  status ENUM('en_attente','payee','annulee') DEFAULT 'en_attente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE lignes_commande (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_commande INT NOT NULL,
  id_produit INT NOT NULL,
  taille_id INT,
  quantite INT NOT NULL DEFAULT 1,
  prix_unitaire DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_commande) REFERENCES commandes(id) ON DELETE CASCADE,
  FOREIGN KEY (id_produit) REFERENCES produits(id),
  FOREIGN KEY (taille_id) REFERENCES tailles(id)
);

INSERT INTO produits (nom, description, description_longue, prix, image, is_active)
VALUES 
('T-shirt Blanc', 'T-shirt officiel', 'T-shirt premium SherWheels Festival', 29.99, '1.png', 1),
('T-shirt Noir', 'T-shirt officiel', 'T-shirt premium SherWheels Festival', 29.99, '2.png', 1),
('Sweat Blanc', 'Sweat officiel', 'Sweat premium SherWheels Festival', 59.99, '3.png', 1),
('Sweat Noir', 'Sweat officiel', 'Sweat premium SherWheels Festival', 59.99, '4.png', 1),
('Polo Blanc', 'Polo officiel', 'Polo premium SherWheels Festival', 49.99, '5.png', 1),
('Polo Noir', 'Polo officiel', 'Polo premium SherWheels Festival', 49.99, '6.png', 1);