-- ============================================================
-- Database: isaaccra_clientsite
-- Author:   Isaac Crft
-- Date:     March 25, 2026
-- Description: Creates and populates the trips and inquiries
--              tables for T's Travel client site.
--              Import this file via phpMyAdmin in cPanel.
-- ============================================================

-- Use the existing database (already created in cPanel)
USE isaaccra_clientsite;

-- ============================================================
-- TABLE 1: trips
-- Stores the four travel package types offered by T's Travel.
-- These match the radio button options on destinations.php.
-- trip_id is the PRIMARY KEY referenced by the inquiries table.
-- ============================================================
CREATE TABLE IF NOT EXISTS trips (
    trip_id          INT(11)        NOT NULL AUTO_INCREMENT,
    trip_name        VARCHAR(100)   NOT NULL,
    trip_type        VARCHAR(20)    NOT NULL,
    description      TEXT           NOT NULL,
    price_per_person DECIMAL(10,2)  NOT NULL,
    max_travelers    INT(11)        NOT NULL,
    PRIMARY KEY (trip_id)
);

-- ============================================================
-- TABLE 2: inquiries
-- Stores every form submission from destinations.php.
-- trip_id is a FOREIGN KEY referencing trips.trip_id.
-- trip_type stores the radio button value submitted on the form
--   (adventure / relaxation / cultural / family) so the inquiry
--   record is self-contained and readable on its own.
-- full_name VARCHAR(50) matches form validation (2-50 chars).
-- travelers INT(11) matches form validation (1-20).
-- submitted_at records the exact date and time of submission.
-- ============================================================
CREATE TABLE IF NOT EXISTS inquiries (
    inquiry_id   INT(11)      NOT NULL AUTO_INCREMENT,
    trip_id      INT(11)      NOT NULL,
    trip_type    VARCHAR(20)  NOT NULL,
    full_name    VARCHAR(50)  NOT NULL,
    travelers    INT(11)      NOT NULL,
    submitted_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (inquiry_id),
    FOREIGN KEY (trip_id) REFERENCES trips(trip_id)
);

-- ============================================================
-- SEED DATA: trips
-- One row per trip type — matches the four radio button values
-- in the form on destinations.php exactly.
-- ============================================================
INSERT INTO trips (trip_name, trip_type, description, price_per_person, max_travelers) VALUES
(
    'Caribbean Paradise',
    'adventure',
    'Experience pristine beaches, crystal-clear waters, and vibrant island culture. Options ranging from all-inclusive resorts to intimate boutique properties.',
    1299.99,
    20
),
(
    'European Adventures',
    'relaxation',
    'Immerse yourself in centuries of history, art, and culture. From the romantic streets of Paris to the ancient ruins of Rome.',
    2499.99,
    15
),
(
    'Romantic Getaways',
    'cultural',
    'Create unforgettable memories with your special someone. Perfect for honeymoons, anniversaries, and special occasions.',
    3199.99,
    10
),
(
    'Family Explorer',
    'family',
    'Fun-filled adventures designed for the whole family. Safe, exciting destinations with activities for all ages.',
    1899.99,
    20
);

-- ============================================================
-- SAMPLE DATA: inquiries
-- Three example submissions showing the full inquiry record
-- including trip_id (foreign key), trip_type (readable label),
-- visitor name, party size, and submission timestamp.
-- ============================================================
INSERT INTO inquiries (trip_id, trip_type, full_name, travelers, submitted_at) VALUES
(1, 'adventure', 'Jane Smith',    2, '2026-03-01 10:30:00'),
(3, 'cultural',  'Carlos Rivera', 2, '2026-03-10 14:15:00'),
(4, 'family',    'Amy Johnson',   4, '2026-03-20 09:45:00');

-- ============================================================
-- TABLE 3: contacts
-- Stores submissions from the contact.php inquiry form.
-- Added to support CHANGE #1 (contact form now INSERTs to DB).
-- ============================================================
CREATE TABLE IF NOT EXISTS contacts (
    contact_id   INT(11)      NOT NULL AUTO_INCREMENT,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(255) NOT NULL,
    phone        VARCHAR(20)  NOT NULL,
    service      VARCHAR(20)  NOT NULL,
    destination  VARCHAR(255) DEFAULT NULL,
    travel_dates VARCHAR(100) DEFAULT NULL,
    travelers    INT(11)      DEFAULT NULL,
    budget       VARCHAR(20)  DEFAULT NULL,
    message      TEXT         DEFAULT NULL,
    submitted_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (contact_id)
);

-- Sample contacts data
INSERT INTO contacts (name, email, phone, service, destination, travel_dates, travelers, budget, message) VALUES
('Michael Torres', 'mtorres@email.com', '555-234-5678', 'cruise', 'Mediterranean', 'July 2026', 2, '5000-10000', 'Looking for a luxury Mediterranean cruise for our anniversary.'),
('Sarah Kim', 'sarah.kim@email.com', '555-345-6789', 'all-inclusive', 'Caribbean', 'December 2026', 4, '2000-5000', 'Family of 4 looking for an all-inclusive beach resort.'),
('David Chen', 'dchen@email.com', '555-456-7890', 'custom', 'Asia', 'March 2027', 2, 'over-10000', 'Interested in a custom Japan and South Korea itinerary.');
