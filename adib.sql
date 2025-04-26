CREATE TABLE preventative_measures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    measure TEXT NOT NULL,
    improvement TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
