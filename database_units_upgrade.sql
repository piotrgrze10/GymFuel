USE gymfuel;

CREATE TABLE IF NOT EXISTS product_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_id INT NOT NULL,
    unit_name VARCHAR(50) NOT NULL,
    unit_display VARCHAR(50) NOT NULL,
    unit_plural VARCHAR(50) NOT NULL,
    weight_in_grams DECIMAL(8,2) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (food_id) REFERENCES food_database(id) ON DELETE CASCADE,
    UNIQUE KEY unique_food_unit (food_id, unit_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_food_units ON product_units(food_id);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Whole Wheat Bread'), 'slice', 'slice', 'slices', 28.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Whole Wheat Bread'), 'gram', 'gram', 'grams', 1.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Apple'), 'gram', 'gram', 'grams', 1.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Apple'), 'piece', 'piece', 'pieces', 182.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Large Egg'), 'egg', 'egg', 'eggs', 50.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Large Egg'), 'gram', 'gram', 'grams', 1.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Banana'), 'piece', 'piece', 'pieces', 120.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Banana'), 'gram', 'gram', 'grams', 1.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Chicken Breast'), 'gram', 'gram', 'grams', 1.00, 1, 1);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Brown Rice'), 'gram', 'gram', 'grams', 1.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Brown Rice'), 'serving', 'serving', 'servings', 150.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Oats'), 'gram', 'gram', 'grams', 1.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Oats'), 'serving', 'serving', 'servings', 40.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Spinach'), 'gram', 'gram', 'grams', 1.00, 1, 1);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Salmon'), 'gram', 'gram', 'grams', 1.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Salmon'), 'serving', 'serving', 'servings', 150.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Greek Yogurt'), 'gram', 'gram', 'grams', 1.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Greek Yogurt'), 'container', 'container', 'containers', 170.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Broccoli'), 'gram', 'gram', 'grams', 1.00, 1, 1);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Sweet Potato'), 'gram', 'gram', 'grams', 1.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Sweet Potato'), 'piece', 'piece', 'pieces', 130.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Peanut Butter'), 'tbsp', 'tbsp', 'tbsp', 16.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Peanut Butter'), 'gram', 'gram', 'grams', 1.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Almonds'), 'gram', 'gram', 'grams', 1.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Almonds'), 'serving', 'serving', 'servings', 28.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Avocado'), 'piece', 'piece', 'pieces', 200.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Avocado'), 'gram', 'gram', 'grams', 1.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Milk'), 'ml', 'ml', 'ml', 1.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Milk'), 'cup', 'cup', 'cups', 240.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Orange'), 'piece', 'piece', 'pieces', 130.00, 1, 1),
((SELECT id FROM food_database WHERE name = 'Orange'), 'gram', 'gram', 'grams', 1.00, 0, 2);

INSERT INTO product_units (food_id, unit_name, unit_display, unit_plural, weight_in_grams, is_default, display_order) VALUES
((SELECT id FROM food_database WHERE name = 'Grapes'), 'gram', 'gram', 'grams', 1.00, 1, 1);

CREATE INDEX idx_default_units ON product_units(food_id, is_default);
CREATE INDEX idx_display_order ON product_units(food_id, display_order);
