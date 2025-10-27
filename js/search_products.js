const rawFoods = {
    'banana': { name: 'Banana', calories: 89, carbs: 23, protein: 1, fat: 0, fiber: 3, image: 'img/products/banana.jpg' },
    'apple': { name: 'Apple', calories: 52, carbs: 14, protein: 0.3, fat: 0.2, fiber: 2.4, image: 'img/products/apple.jpg' },
    'orange': { name: 'Orange', calories: 47, carbs: 12, protein: 0.9, fat: 0.1, fiber: 2.4, image: 'img/products/orange.jpg' },
    'lime': { name: 'Lime', calories: 30, carbs: 11, protein: 0.7, fat: 0.2, fiber: 2.8, image: 'https://images.unsplash.com/photo-1618317953867-9d0c4e0cccd9' },
    'lemon': { name: 'Lemon', calories: 29, carbs: 9, protein: 1.1, fat: 0.3, fiber: 2.8, image: 'https://images.unsplash.com/photo-1590502593747-42a996133562' },
    'grapefruit': { name: 'Grapefruit', calories: 42, carbs: 11, protein: 0.8, fat: 0.1, fiber: 1.6, image: 'https://images.unsplash.com/photo-1542303665-ceaad6c338ca' },
    'strawberry': { name: 'Strawberry', calories: 32, carbs: 8, protein: 0.7, fat: 0.3, fiber: 2, image: 'img/products/strawberry.jpg' },
    'blueberry': { name: 'Blueberry', calories: 57, carbs: 14, protein: 0.7, fat: 0.3, fiber: 2.4, image: 'img/products/blueberry.jpg' },
    'raspberry': { name: 'Raspberry', calories: 52, carbs: 12, protein: 1.2, fat: 0.7, fiber: 6.5, image: 'img/products/raspberry.jpg' },
    'grape': { name: 'Grape', calories: 62, carbs: 16, protein: 0.6, fat: 0.2, fiber: 1, image: 'img/products/grape.jpg' },
    'pineapple': { name: 'Pineapple', calories: 50, carbs: 13, protein: 0.5, fat: 0.1, fiber: 1.4, image: 'https://images.unsplash.com/photo-1571494146906-86de15d3817b' },
    'mango': { name: 'Mango', calories: 60, carbs: 15, protein: 0.8, fat: 0.4, fiber: 1.6, image: 'https://images.unsplash.com/photo-1607365705782-b032c18299b2' },
    'kiwi': { name: 'Kiwi', calories: 61, carbs: 15, protein: 1.1, fat: 0.5, fiber: 3, image: 'https://images.unsplash.com/photo-1582139329536-e7284fece509' },
    'watermelon': { name: 'Watermelon', calories: 30, carbs: 8, protein: 0.6, fat: 0.2, fiber: 0.4, image: 'https://images.unsplash.com/photo-1602834385822-321a97360e1c' },
    'papaya': { name: 'Papaya', calories: 43, carbs: 11, protein: 0.5, fat: 0.3, fiber: 1.7, image: 'https://images.unsplash.com/photo-1615485925330-deb215fff32b' },
    'peach': { name: 'Peach', calories: 39, carbs: 10, protein: 0.9, fat: 0.3, fiber: 1.5, image: 'https://images.unsplash.com/photo-1607522377509-f14335f0b1f5' },
    'pear': { name: 'Pear', calories: 57, carbs: 15, protein: 0.4, fat: 0.1, fiber: 3.1, image: 'https://images.unsplash.com/photo-1590133762612-9e8e6e5c06b2' },
    'cherry': { name: 'Cherry', calories: 50, carbs: 12, protein: 1, fat: 0.3, fiber: 1.6, image: 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c' },
    'avocado': { name: 'Avocado', calories: 160, carbs: 9, protein: 2, fat: 15, fiber: 7, image: 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578' },
    'broccoli': { name: 'Broccoli', calories: 34, carbs: 7, protein: 2.8, fat: 0.4, fiber: 2.6, image: 'img/products/broccoli.jpg' },
    'spinach': { name: 'Spinach', calories: 23, carbs: 4, protein: 2.9, fat: 0.4, fiber: 2.2, image: 'img/products/spinach.jpg' },
    'carrot': { name: 'Carrot', calories: 41, carbs: 10, protein: 0.9, fat: 0.2, fiber: 2.8, image: 'img/products/carrot.jpg' },
    'potato': { name: 'Potato', calories: 77, carbs: 17, protein: 2, fat: 0.1, fiber: 2.2, image: 'img/products/potato.jpg' },
    'tomato': { name: 'Tomato', calories: 18, carbs: 4, protein: 0.9, fat: 0.2, fiber: 1.2, image: 'https://images.unsplash.com/photo-1547272630-9130b6fd5fe6' },
    'cucumber': { name: 'Cucumber', calories: 16, carbs: 4, protein: 0.7, fat: 0.1, fiber: 0.5, image: 'https://images.unsplash.com/photo-1604977043462-598316d636b4' },
    'pepper': { name: 'Bell Pepper', calories: 31, carbs: 7, protein: 1, fat: 0.3, fiber: 2.1, image: 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83' },
    'onion': { name: 'Onion', calories: 40, carbs: 9, protein: 1.1, fat: 0.1, fiber: 1.7, image: 'https://images.unsplash.com/photo-1618512496249-4b927a89d2e6' },
    'lettuce': { name: 'Lettuce', calories: 15, carbs: 3, protein: 1.4, fat: 0.2, fiber: 1.3, image: 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1' },
    'cabbage': { name: 'Cabbage', calories: 25, carbs: 6, protein: 1.3, fat: 0.1, fiber: 2.5, image: 'https://images.unsplash.com/photo-1606166737429-ecb31f679dfc' },
    'zucchini': { name: 'Zucchini', calories: 17, carbs: 3, protein: 1.2, fat: 0.3, fiber: 1, image: 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1' },
    'chicken breast': { name: 'Chicken Breast', calories: 165, carbs: 0, protein: 31, fat: 3.6, fiber: 0, image: 'img/products/chicken.jpg' },
    'chicken': { name: 'Chicken Breast', calories: 165, carbs: 0, protein: 31, fat: 3.6, fiber: 0, image: 'img/products/chicken.jpg' },
    'salmon': { name: 'Salmon', calories: 208, carbs: 0, protein: 20, fat: 12, fiber: 0, image: 'img/products/salmon.jpg' },
    'tuna': { name: 'Tuna', calories: 132, carbs: 0, protein: 30, fat: 1, fiber: 0, image: 'img/products/tuna.jpg' },
    'pork': { name: 'Pork', calories: 242, carbs: 0, protein: 27, fat: 14, fiber: 0, image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791' },
    'beef': { name: 'Beef', calories: 250, carbs: 0, protein: 26, fat: 15, fiber: 0, image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791' },
    'turkey': { name: 'Turkey', calories: 135, carbs: 0, protein: 30, fat: 1, fiber: 0, image: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791' },
    'eggs': { name: 'Eggs', calories: 155, carbs: 1.1, protein: 13, fat: 11, fiber: 0, image: 'img/products/eggs.jpg' },
    'egg': { name: 'Egg', calories: 155, carbs: 1.1, protein: 13, fat: 11, fiber: 0, image: 'img/products/eggs.jpg' },
    'greek yogurt': { name: 'Greek Yogurt', calories: 97, carbs: 4, protein: 10, fat: 5, fiber: 0, image: 'img/products/yogurt.jpg' },
    'yogurt': { name: 'Yogurt', calories: 59, carbs: 4, protein: 10, fat: 0.4, fiber: 0, image: 'img/products/yogurt.jpg' },
    'milk': { name: 'Milk', calories: 42, carbs: 5, protein: 3.4, fat: 1, fiber: 0, image: 'img/products/milk.jpg' },
    'cheese': { name: 'Cheese', calories: 361, carbs: 1.3, protein: 23, fat: 29, fiber: 0, image: 'img/products/cheese.jpg' },
    'butter': { name: 'Butter', calories: 717, carbs: 0.1, protein: 0.9, fat: 81, fiber: 0, image: 'img/products/butter.jpg' },
    'rice': { name: 'White Rice', calories: 130, carbs: 28, protein: 2.7, fat: 0.3, fiber: 0.4, image: 'img/products/rice.jpg' },
    'oats': { name: 'Oats', calories: 389, carbs: 66, protein: 17, fat: 7, fiber: 11, image: 'img/products/oats.jpg' },
    'oatmeal': { name: 'Oats', calories: 389, carbs: 66, protein: 17, fat: 7, fiber: 11, image: 'img/products/oats.jpg' },
    'pasta': { name: 'Pasta', calories: 131, carbs: 25, protein: 5, fat: 1.1, fiber: 1.8, image: 'img/products/pasta.jpg' },
    'bread': { name: 'Bread', calories: 265, carbs: 49, protein: 9, fat: 3.2, fiber: 2.7, image: 'img/products/bread.jpg' },
    'quinoa': { name: 'Quinoa', calories: 368, carbs: 64, protein: 14, fat: 6, fiber: 7, image: 'https://images.unsplash.com/photo-1595044429147-f247efcd5dc8' },
    'barley': { name: 'Barley', calories: 354, carbs: 73, protein: 12, fat: 2.3, fiber: 17, image: 'https://images.unsplash.com/photo-1595044429147-f247efcd5dc8' },
    'almond': { name: 'Almonds', calories: 579, carbs: 22, protein: 21, fat: 50, fiber: 12, image: 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c' },
    'walnut': { name: 'Walnuts', calories: 654, carbs: 14, protein: 15, fat: 65, fiber: 7, image: 'https://images.unsplash.com/photo-1591541203057-c9e2bf3a5e70' },
    'peanut': { name: 'Peanuts', calories: 567, carbs: 16, protein: 26, fat: 49, fiber: 8, image: 'https://images.unsplash.com/photo-1612685449255-0a0dfb12e70b' },
    'cashew': { name: 'Cashews', calories: 553, carbs: 30, protein: 18, fat: 44, fiber: 3, image: 'https://images.unsplash.com/photo-1579783901586-d88db74b4fe1' },
    'pistachio': { name: 'Pistachio', calories: 560, carbs: 28, protein: 20, fat: 45, fiber: 10, image: 'https://images.unsplash.com/photo-1589354775484-757f36d06cc0' },
    'hazelnut': { name: 'Hazelnut', calories: 628, carbs: 17, protein: 15, fat: 61, fiber: 10, image: 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c' },
    'cherries': { name: 'Cherries', calories: 63, carbs: 16, protein: 1, fat: 0.2, fiber: 2.1, image: 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c' },
    'grapes': { name: 'Grapes', calories: 69, carbs: 18, protein: 0.7, fat: 0.2, fiber: 0.9, image: 'https://images.unsplash.com/photo-1590674899484-d5640e854abe' },
    'plum': { name: 'Plum', calories: 46, carbs: 11, protein: 0.7, fat: 0.3, fiber: 1.4, image: 'https://images.unsplash.com/photo-1580052614034-c55d20bfee3b' },
    'apricot': { name: 'Apricot', calories: 48, carbs: 11, protein: 1.4, fat: 0.4, fiber: 2, image: 'https://images.unsplash.com/photo-1617173134476-365bdba73831' },
    'fig': { name: 'Fig', calories: 74, carbs: 19, protein: 0.8, fat: 0.3, fiber: 3, image: 'https://images.unsplash.com/photo-1618317953867-9d0c4e0cccd9' },
    'dates': { name: 'Dates', calories: 282, carbs: 75, protein: 2.5, fat: 0.4, fiber: 8, image: 'https://images.unsplash.com/photo-1607609184279-d8fb2a723f0c' },
    'cantaloupe': { name: 'Cantaloupe', calories: 34, carbs: 8, protein: 0.8, fat: 0.2, fiber: 0.9, image: 'https://images.unsplash.com/photo-1605027990121-3c12d53c3a0d' },
    'asparagus': { name: 'Asparagus', calories: 20, carbs: 4, protein: 2.2, fat: 0.1, fiber: 2.1, image: 'https://images.unsplash.com/photo-1623428187969-5da2dcea5ebf' },
    'cauliflower': { name: 'Cauliflower', calories: 25, carbs: 5, protein: 1.9, fat: 0.3, fiber: 2, image: 'https://images.unsplash.com/photo-1534920335396-b70b47fd09d6' },
    'bell pepper': { name: 'Bell Pepper', calories: 31, carbs: 7, protein: 1, fat: 0.3, fiber: 2.5, image: 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83' },
    'mushrooms': { name: 'Mushrooms', calories: 22, carbs: 3, protein: 3.1, fat: 0.3, fiber: 1, image: 'https://images.unsplash.com/photo-1563805042-7684c019e1b5' },
    'garlic': { name: 'Garlic', calories: 149, carbs: 33, protein: 6.4, fat: 0.5, fiber: 2.1, image: 'https://images.unsplash.com/photo-1618512496249-4b927a89d2e6' },
    'corn': { name: 'Corn', calories: 86, carbs: 19, protein: 3.3, fat: 1.2, fiber: 2.7, image: 'https://images.unsplash.com/photo-1534920335396-b70b47fd09d6' },
    'peas': { name: 'Green Peas', calories: 81, carbs: 14, protein: 5, fat: 0.4, fiber: 5, image: 'https://images.unsplash.com/photo-1623428187969-5da2dcea5ebf' },
    'green beans': { name: 'Green Beans', calories: 31, carbs: 7, protein: 1.8, fat: 0.2, fiber: 2.7, image: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea' },
    'white fish': { name: 'White Fish', calories: 144, carbs: 0, protein: 31, fat: 1, fiber: 0, image: 'https://images.unsplash.com/photo-1574781330853-d0db8cc7800d' },
    'cod': { name: 'Cod', calories: 82, carbs: 0, protein: 18, fat: 0.7, fiber: 0, image: 'https://images.unsplash.com/photo-1574781330853-d0db8cc7800d' },
    'mackerel': { name: 'Mackerel', calories: 262, carbs: 0, protein: 19, fat: 18, fiber: 0, image: 'https://images.unsplash.com/photo-1574781330853-d0db8cc7800d' },
    'herring': { name: 'Herring', calories: 203, carbs: 0, protein: 18, fat: 14, fiber: 0, image: 'https://images.unsplash.com/photo-1574781330853-d0db8cc7800d' },
    'shrimp': { name: 'Shrimp', calories: 99, carbs: 0.2, protein: 24, fat: 0.3, fiber: 0, image: 'https://images.unsplash.com/photo-1559339352-11d035aa65de' },
    'cottage cheese': { name: 'Cottage Cheese', calories: 98, carbs: 3, protein: 11, fat: 4, fiber: 0, image: 'https://images.unsplash.com/photo-1452195100486-9cc805987862' },
    'skyr': { name: 'Skyr', calories: 59, carbs: 4, protein: 11, fat: 0.2, fiber: 0, image: 'https://images.unsplash.com/photo-1488477181946-6428a0291777' },
    'goat cheese': { name: 'Goat Cheese', calories: 364, carbs: 2.5, protein: 22, fat: 28, fiber: 0, image: 'https://images.unsplash.com/photo-1452195100486-9cc805987862' },
    'feta': { name: 'Feta Cheese', calories: 264, carbs: 4, protein: 14, fat: 21, fiber: 0, image: 'https://images.unsplash.com/photo-1452195100486-9cc805987862' },
    'ricotta': { name: 'Ricotta', calories: 174, carbs: 3, protein: 11, fat: 13, fiber: 0, image: 'https://images.unsplash.com/photo-1452195100486-9cc805987862' },
    'brown rice': { name: 'Brown Rice', calories: 111, carbs: 23, protein: 2.6, fat: 0.9, fiber: 1.8, image: 'https://images.unsplash.com/photo-1516684669134-de6f7d473a8a' },
    'wild rice': { name: 'Wild Rice', calories: 101, carbs: 21, protein: 4, fat: 0.3, fiber: 1.8, image: 'https://images.unsplash.com/photo-1516684669134-de6f7d473a8a' },
    'couscous': { name: 'Couscous', calories: 112, carbs: 23, protein: 3.8, fat: 0.2, fiber: 1.4, image: 'https://images.unsplash.com/photo-1595044429147-f247efcd5dc8' },
    'buckwheat': { name: 'Buckwheat', calories: 343, carbs: 72, protein: 13, fat: 3.4, fiber: 10, image: 'https://images.unsplash.com/photo-1543489822-c495fc0924ba' },
    'bulgur': { name: 'Bulgur', calories: 83, carbs: 19, protein: 3.1, fat: 0.2, fiber: 4.5, image: 'https://images.unsplash.com/photo-1534920335396-b70b47fd09d6' },
    'chickpeas': { name: 'Chickpeas', calories: 164, carbs: 27, protein: 9, fat: 2.6, fiber: 8, image: 'https://images.unsplash.com/photo-1543489822-c495fc0924ba' },
    'lentils': { name: 'Lentils', calories: 116, carbs: 20, protein: 9, fat: 0.4, fiber: 8, image: 'https://images.unsplash.com/photo-1543489822-c495fc0924ba' },
    'black beans': { name: 'Black Beans', calories: 132, carbs: 24, protein: 9, fat: 0.5, fiber: 8.7, image: 'https://images.unsplash.com/photo-1543489822-c495fc0924ba' },
    'kidney beans': { name: 'Kidney Beans', calories: 127, carbs: 23, protein: 9, fat: 0.5, fiber: 6.4, image: 'https://images.unsplash.com/photo-1543489822-c495fc0924ba' },
    'protein powder': { name: 'Whey Protein', calories: 120, carbs: 3, protein: 24, fat: 1.5, fiber: 0, image: 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2' },
    'whey': { name: 'Whey Protein', calories: 120, carbs: 3, protein: 24, fat: 1.5, fiber: 0, image: 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2' },
    'protein shake': { name: 'Protein Shake', calories: 100, carbs: 2, protein: 20, fat: 1, fiber: 1, image: 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2' },
    'protein bar': { name: 'Protein Bar', calories: 200, carbs: 15, protein: 20, fat: 7, fiber: 3, image: 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445' },
    'iso whey': { name: 'Whey Isolate', calories: 110, carbs: 1, protein: 27, fat: 0.5, fiber: 0, image: 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2' },
    'casein': { name: 'Casein Protein', calories: 120, carbs: 3, protein: 24, fat: 1.5, fiber: 0, image: 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2' },
    'vegan protein': { name: 'Vegan Protein', calories: 110, carbs: 3, protein: 22, fat: 1, fiber: 2, image: 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2' },
    'cereal': { name: 'Cereal', calories: 381, carbs: 85, protein: 8, fat: 3.4, fiber: 2, image: 'https://images.unsplash.com/photo-1620075267033-0272a4d13fad' },
    'overnight oats': { name: 'Overnight Oats', calories: 68, carbs: 12, protein: 2.4, fat: 1.4, fiber: 1.7, image: 'https://images.unsplash.com/photo-1564540583349-766414dccc6d' },
    'pancakes': { name: 'Pancakes', calories: 227, carbs: 28, protein: 6, fat: 9, fiber: 1.2, image: 'https://images.unsplash.com/photo-1528207779866-1b4297975b4d' },
    'waffles': { name: 'Waffles', calories: 291, carbs: 37, protein: 8, fat: 11, fiber: 1.8, image: 'https://images.unsplash.com/photo-1562376552-0d160a2f238d' },
    'coffee': { name: 'Coffee', calories: 2, carbs: 0, protein: 0.1, fat: 0, fiber: 0, image: 'https://images.unsplash.com/photo-1514432324605-a09f9b479a69' },
    'green tea': { name: 'Green Tea', calories: 2, carbs: 0, protein: 0.2, fat: 0, fiber: 0, image: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc' },
    'black tea': { name: 'Black Tea', calories: 2, carbs: 0.3, protein: 0.2, fat: 0, fiber: 0, image: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc' },
    'orange juice': { name: 'Orange Juice', calories: 45, carbs: 11, protein: 0.7, fat: 0.2, fiber: 0.2, image: 'https://images.unsplash.com/photo-1580052614034-c55d20bfee3b' },
    'apple juice': { name: 'Apple Juice', calories: 45, carbs: 11, protein: 0.1, fat: 0.1, fiber: 0.2, image: 'https://images.unsplash.com/photo-1619546813926-a78fa6372cd2' },
    'coconut water': { name: 'Coconut Water', calories: 19, carbs: 4, protein: 0.7, fat: 0.2, fiber: 1.1, image: 'https://images.unsplash.com/photo-1532205184558-d5f9e5f3477d' },
    'dark chocolate': { name: 'Dark Chocolate', calories: 546, carbs: 46, protein: 8, fat: 31, fiber: 11, image: 'https://images.unsplash.com/photo-1599032588431-32ec6baa621f' },
    'honey': { name: 'Honey', calories: 304, carbs: 82, protein: 0.3, fat: 0, fiber: 0.2, image: 'https://images.unsplash.com/photo-1587049352846-4a222e784d38' },
    'maple syrup': { name: 'Maple Syrup', calories: 260, carbs: 67, protein: 0.04, fat: 0.06, fiber: 0, image: 'https://images.unsplash.com/photo-1605027990121-3c12d53c3a0d' },
    'peanut butter': { name: 'Peanut Butter', calories: 588, carbs: 20, protein: 25, fat: 50, fiber: 6, image: 'https://images.unsplash.com/photo-1612685449255-0a0dfb12e70b' },
    'almond butter': { name: 'Almond Butter', calories: 614, carbs: 19, protein: 21, fat: 55, fiber: 11, image: 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c' },
    'olive oil': { name: 'Olive Oil', calories: 884, carbs: 0, protein: 0, fat: 100, fiber: 0, image: 'https://images.unsplash.com/photo-1523339560800-ff4a3b16ba94' },
    'coconut oil': { name: 'Coconut Oil', calories: 862, carbs: 0, protein: 0, fat: 100, fiber: 0, image: 'https://images.unsplash.com/photo-1617099895738-0552821e19c4' },
    'avocado oil': { name: 'Avocado Oil', calories: 884, carbs: 0, protein: 0, fat: 100, fiber: 0, image: 'https://images.unsplash.com/photo-1607513746994-51f730a44832' },
    'turmeric': { name: 'Turmeric', calories: 354, carbs: 65, protein: 8, fat: 10, fiber: 21, image: 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7' },
    'cinnamon': { name: 'Cinnamon', calories: 247, carbs: 81, protein: 4, fat: 1.2, fiber: 53, image: 'https://images.unsplash.com/photo-1599742151974-4d1421503918' },
    'ginger': { name: 'Ginger', calories: 80, carbs: 18, protein: 1.8, fat: 0.8, fiber: 2, image: 'https://images.unsplash.com/photo-1587831990711-23ca6441447b' }
};

let searchTimeout;
let allProducts = [];
let currentPage = 1;
const productsPerPage = 12;
let currentProduct = null;

const categoryTerms = {
    'fruits': ['apple', 'banana', 'orange', 'grape', 'strawberry', 'blueberry'],
    'vegetables': ['broccoli', 'carrot', 'spinach', 'tomato', 'cucumber'],
    'meat': ['chicken', 'beef', 'pork', 'salmon', 'turkey'],
    'dairy': ['milk', 'cheese', 'yogurt', 'butter'],
    'grains': ['rice', 'oats', 'quinoa', 'bread', 'pasta'],
    'nuts': ['almond', 'walnut', 'peanut', 'cashew'],
    'drinks': ['water', 'juice', 'coffee', 'tea'],
    'snacks': ['chips', 'cookies', 'crackers', 'nuts'],
    'protein': ['chicken breast', 'eggs', 'salmon', 'tuna', 'protein bar', 'greek yogurt'],
    'breakfast': ['oats', 'eggs', 'cereal', 'yogurt', 'pancake', 'waffle'],
    'alcohol': ['wine', 'beer', 'whisky', 'vodka', 'rum', 'gin'],
    'others': ['chocolate', 'honey', 'jam', 'sauce', 'oil', 'vinegar']
};

function addToRecentSearches(query) {
    if (!query || query.trim().length < 2) return;
    let recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
    recentSearches = recentSearches.filter(item => item !== query);
    recentSearches.unshift(query);
    recentSearches = recentSearches.slice(0, 5);
    localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
    renderRecentSearches();
}

function renderRecentSearches() {
    const container = document.getElementById('recentSearchesList');
    const containerWrapper = document.getElementById('recentSearchesContainer');
    const recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
    
    if (recentSearches.length === 0) {
        containerWrapper.style.display = 'none';
        return;
    }
    
    containerWrapper.style.display = 'block';
    let html = '';
    
    recentSearches.forEach(search => {
        html += `
            <div class="recent-search-chip" data-query="${search}">
                <i class="fa-solid fa-clock"></i>
                <span>${search}</span>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    document.querySelectorAll('.recent-search-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const query = this.getAttribute('data-query');
            document.getElementById('searchInput').value = query;
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('loadingSpinner').classList.add('show');
            document.getElementById('results').innerHTML = '';
            searchProducts(query);
        });
    });
}

window.clearCategorySelection = function() {
    document.getElementById('results').innerHTML = '';
    allProducts = [];
    currentPage = 1;
    document.getElementById('paginationContainer').style.display = 'none';
    document.getElementById('categoriesSection').classList.remove('hidden');
    renderRecentSearches();
    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('clearCategoryWrapper').style.display = 'none';
    document.getElementById('searchInput').value = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

async function searchProducts(query) {
    try {
        addToRecentSearches(query);
        document.getElementById('loadingSpinner').classList.remove('show');
        
        const lowerQuery = query.toLowerCase();
        const matchedProducts = [];
        
        for (const [key, food] of Object.entries(rawFoods)) {
            if (key === lowerQuery || key.includes(lowerQuery) || lowerQuery.includes(key) || food.name.toLowerCase().includes(lowerQuery)) {
                const product = {
                    product_name: food.name,
                    brands: 'Natural Food',
                    nutriments: {
                        'energy-kcal': food.calories,
                        'carbohydrates': food.carbs,
                        'proteins': food.protein,
                        'fat': food.fat,
                        'fiber': food.fiber || 0
                    },
                    is_raw_food: true,
                    image_url: food.image || null
                };
                matchedProducts.push(product);
            }
        }
        
        matchedProducts.sort((a, b) => {
            const nameA = a.product_name.toLowerCase();
            const nameB = b.product_name.toLowerCase();
            const aExact = nameA === lowerQuery;
            const bExact = nameB === lowerQuery;
            
            if (aExact && !bExact) return -1;
            if (!aExact && bExact) return 1;
            
            const aStarts = nameA.startsWith(lowerQuery);
            const bStarts = nameB.startsWith(lowerQuery);
            
            if (aStarts && !bStarts) return -1;
            if (!aStarts && bStarts) return 1;
            return 0;
        });
        
        if (matchedProducts.length > 0) {
            displayProducts(matchedProducts);
        } else {
            showNoResults();
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('loadingSpinner').classList.remove('show');
        showError('Error searching products. Please try again.');
    }
}

async function searchByCategory(category) {
    try {
        const terms = categoryTerms[category];
        let productsArray = [];
        
        for (let term of terms) {
            const lowerTerm = term.toLowerCase();
            if (rawFoods[lowerTerm]) {
                const rawFood = rawFoods[lowerTerm];
                const rawProduct = {
                    product_name: rawFood.name,
                    brands: 'Raw Food',
                    nutriments: {
                        'energy-kcal': rawFood.calories,
                        'carbohydrates': rawFood.carbs,
                        'proteins': rawFood.protein,
                        'fat': rawFood.fat
                    },
                    is_raw_food: true,
                    image_url: rawFood.image || null
                };
                productsArray.push(rawProduct);
            }
        }
        
        document.getElementById('loadingSpinner').classList.remove('show');
        
        if (productsArray.length > 0) {
            const categoryNames = {
                'fruits': 'Fruits',
                'vegetables': 'Vegetables',
                'meat': 'Meat & Fish',
                'dairy': 'Dairy',
                'grains': 'Grains & Cereals',
                'nuts': 'Nuts & Seeds',
                'drinks': 'Beverages',
                'snacks': 'Snacks',
                'protein': 'Protein Foods',
                'breakfast': 'Breakfast',
                'alcohol': 'Alcohol',
                'others': 'Others'
            };
            
            document.getElementById('activeCategoryName').textContent = `Showing: ${categoryNames[category]}`;
            document.getElementById('clearCategoryWrapper').style.display = 'block';
            displayProducts(productsArray);
            document.getElementById('categoriesSection').classList.add('hidden');
        } else {
            showNoResults();
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('loadingSpinner').classList.remove('show');
        showError('Error searching products. Please try again.');
    }
}

function displayProducts(products) {
    allProducts = products;
    currentPage = 1;
    
    if (products.length > 0) {
        document.getElementById('categoriesSection').classList.add('hidden');
        document.getElementById('recentSearchesContainer').style.display = 'none';
    }
    
    document.getElementById('paginationContainer').style.display = 'none';
    
    const activeCategory = document.querySelector('.category-btn.active');
    if (!activeCategory && document.getElementById('searchInput').value.length >= 2) {
        document.getElementById('clearCategoryWrapper').style.display = 'none';
    }
    
    renderProducts();
    
    setTimeout(() => {
        document.getElementById('resultsScrollTarget').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }, 100);
}

function renderProducts() {
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '';
    
    if (allProducts.length === 0) {
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }
    
    const wrapper = document.createElement('div');
    wrapper.className = 'results-wrapper';
    
    const totalPages = Math.ceil(allProducts.length / productsPerPage);
    const startIndex = (currentPage - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;
    const productsToShow = allProducts.slice(startIndex, endIndex);
    
    productsToShow.forEach(product => {
        if (!product.product_name || !product.nutriments) return;
        
        const nutrition = product.nutriments;
        const calories = nutrition['energy-kcal'] != null ? Math.round(nutrition['energy-kcal']) : null;
        const carbs = nutrition['carbohydrates'] != null ? Math.round(nutrition['carbohydrates']) : null;
        const protein = nutrition['proteins'] != null ? Math.round(nutrition['proteins']) : null;
        const fat = nutrition['fat'] != null ? Math.round(nutrition['fat']) : null;
        
        let productName = product.product_name.trim().replace(/\s+/g, ' ');
        const lowerName = productName.toLowerCase();
        
        const skipPatterns = [
            /\bblanc\s+de\s+poulet\b/i,
            /\bfilet\s+de\s+poulet\b/i,
            /\btranches\s+de\s+poulet\b/i,
            /\bpoulet\s+de\s+/i
        ];
        
        if (skipPatterns.some(pattern => pattern.test(lowerName))) {
            return;
        }
        
        if (productName.length > 60) {
            productName = productName.substring(0, 60) + '...';
        }
        
        const card = document.createElement('div');
        card.className = 'product-card';
        
        let imageUrl = product.image_url || product.image_front_url || product.image_small_url || product.image_front_small_url;
        const isRawFood = product.is_raw_food || false;
        
        if (isRawFood && rawFoods[lowerName] && rawFoods[lowerName].image) {
            imageUrl = rawFoods[lowerName].image;
        }
        
        let placeholderIcon = getPlaceholderIcon(productName);
        let imageHTML;
        
        if (imageUrl) {
            imageHTML = `<div class="product-image-wrapper"><img src="${imageUrl}" alt="${productName}" loading="lazy" decoding="async" style="object-fit: cover; width: 100%; height: 100%;" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'product-image-placeholder\\'><i class=\\'fa-solid ${placeholderIcon}\\'></i></div>';"></div>`;
        } else {
            imageHTML = `<div class="product-image-wrapper"><div class="product-image-placeholder"><i class="fa-solid ${placeholderIcon}"></i></div></div>`;
        }
        
        const displayCal = calories !== null && calories !== undefined ? calories : '-';
        const displayProtein = protein !== null && protein !== undefined ? protein : '-';
        const displayCarbs = carbs !== null && carbs !== undefined ? carbs : '-';
        const displayFat = fat !== null && fat !== undefined ? fat : '-';
        
        card.innerHTML = `
            ${imageHTML}
            <div class="product-content">
                <div class="product-info">
                    <div class="product-name">${productName}</div>
                    <div class="product-brand">${product.brands || 'No brand'}</div>
                </div>
                <div class="nutrition-section">
                    <div class="nutrition-badges">
                        <div class="nutrition-badge"><div class="badge-value">${displayCal}</div><div class="badge-label">Cal</div></div>
                        <div class="nutrition-badge"><div class="badge-value">${displayProtein}g</div><div class="badge-label">Protein</div></div>
                        <div class="nutrition-badge"><div class="badge-value">${displayCarbs}g</div><div class="badge-label">Carbs</div></div>
                        <div class="nutrition-badge"><div class="badge-value">${displayFat}g</div><div class="badge-label">Fat</div></div>
                    </div>
                    <div class="nutrition-note">per 100g</div>
                </div>
            </div>
        `;
        
        card.onclick = () => openCalculator(product);
        card.style.cursor = 'pointer';
        wrapper.appendChild(card);
    });
    
    resultsDiv.appendChild(wrapper);
    updatePaginationUI(totalPages);
}

function getPlaceholderIcon(productName) {
    const productNameLower = productName.toLowerCase();
    
    if (productNameLower.includes('apple') || productNameLower.includes('banana') || productNameLower.includes('orange') || productNameLower.includes('berry') || productNameLower.includes('strawberry') || productNameLower.includes('blueberry') || productNameLower.includes('raspberry') || productNameLower.includes('fruit')) {
        return 'fa-apple-whole';
    } else if (productNameLower.includes('tomato') || productNameLower.includes('carrot') || productNameLower.includes('broccoli') || productNameLower.includes('spinach') || productNameLower.includes('vegetable') || productNameLower.includes('pepper') || productNameLower.includes('cucumber') || productNameLower.includes('lettuce') || productNameLower.includes('cabbage') || productNameLower.includes('mushroom') || productNameLower.includes('garlic') || productNameLower.includes('onion')) {
        return 'fa-carrot';
    } else if (productNameLower.includes('chicken') || productNameLower.includes('pork') || productNameLower.includes('beef') || productNameLower.includes('turkey')) {
        return 'fa-drumstick-bite';
    } else if (productNameLower.includes('salmon') || productNameLower.includes('tuna') || productNameLower.includes('fish') || productNameLower.includes('shrimp') || productNameLower.includes('cod') || productNameLower.includes('mackerel') || productNameLower.includes('herring')) {
        return 'fa-fish';
    } else if (productNameLower.includes('egg')) {
        return 'fa-egg';
    } else if (productNameLower.includes('yogurt') || productNameLower.includes('cheese') || productNameLower.includes('milk') || productNameLower.includes('cottage') || productNameLower.includes('skyr') || productNameLower.includes('feta') || productNameLower.includes('ricotta') || productNameLower.includes('butter')) {
        return 'fa-cheese';
    } else if (productNameLower.includes('bread') || productNameLower.includes('pasta') || productNameLower.includes('rice') || productNameLower.includes('oats') || productNameLower.includes('quinoa') || productNameLower.includes('couscous') || productNameLower.includes('barley') || productNameLower.includes('buckwheat') || productNameLower.includes('bulgur')) {
        return 'fa-wheat-awn';
    } else if (productNameLower.includes('protein') || productNameLower.includes('whey') || productNameLower.includes('casein') || productNameLower.includes('iso whey')) {
        return 'fa-dumbbell';
    } else if (productNameLower.includes('almond') || productNameLower.includes('walnut') || productNameLower.includes('peanut') || productNameLower.includes('cashew') || productNameLower.includes('pistachio') || productNameLower.includes('hazelnut')) {
        return 'fa-stroopwafel';
    } else if (productNameLower.includes('coffee') || productNameLower.includes('tea') || productNameLower.includes('juice')) {
        return 'fa-mug-hot';
    } else if (productNameLower.includes('oil')) {
        return 'fa-bottle-droplet';
    }
    return 'fa-bowl-food';
}

function updatePaginationUI(totalPages) {
    const paginationContainer = document.getElementById('paginationContainer');
    const paginationInfo = document.getElementById('paginationInfo');
    
    if (totalPages <= 1 || allProducts.length === 0) {
        paginationContainer.style.display = 'none';
        return;
    }
    
    paginationContainer.style.display = 'flex';
    paginationInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
}

function changePage(direction) {
    const totalPages = Math.ceil(allProducts.length / productsPerPage);
    
    if (direction === -1 && currentPage > 1) {
        currentPage--;
    } else if (direction === 1 && currentPage < totalPages) {
        currentPage++;
    }
    
    renderProducts();
    
    setTimeout(() => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 100);
}

function openCalculator(product, index) {
    currentProduct = product;
    document.getElementById('calcProductName').textContent = product.product_name;
    
    const imageContainer = document.getElementById('calcProductImage');
    let imageUrl = product.image_url || product.image_front_url || product.image_small_url || product.image_front_small_url;
    const lowerName = product.product_name.toLowerCase();
    const isRawFood = product.is_raw_food || false;
    
    if (isRawFood && rawFoods[lowerName] && rawFoods[lowerName].image) {
        imageUrl = rawFoods[lowerName].image;
    }
    
    let placeholderIcon = getPlaceholderIcon(product.product_name);
    
    if (imageUrl) {
        imageContainer.innerHTML = `<img src="${imageUrl}" alt="${product.product_name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" loading="eager" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\\'fa-solid ${placeholderIcon}\\' style=\\'font-size: 32px; color: white;\\'></i>'">`;
    } else {
        imageContainer.innerHTML = `<i class="fa-solid ${placeholderIcon}" style="font-size: 32px; color: white;"></i>`;
    }
    
    const servingOptions = document.getElementById('servingOptions');
    servingOptions.innerHTML = '';
    
    const servings = [
        { multiplier: 0.5, label: '0.5×', weight: '50 g' },
        { multiplier: 1, label: '1×', weight: '100 g' },
        { multiplier: 2, label: '2×', weight: '200 g' },
        { multiplier: 3, label: '3×', weight: '300 g' },
        { multiplier: 0.25, label: '0.25×', weight: '25 g' },
        { multiplier: 1.5, label: '1.5×', weight: '150 g' }
    ];
    
    servings.forEach(serving => {
        const btn = document.createElement('div');
        btn.className = 'serving-btn';
        btn.dataset.multiplier = serving.multiplier;
        btn.innerHTML = `${serving.label}<br><small>${serving.weight}</small>`;
        btn.onclick = () => selectServing(serving.multiplier);
        servingOptions.appendChild(btn);
    });
    
    selectServing(1);
    document.getElementById('calculatorModal').classList.add('active');
}

function selectServing(multiplier) {
    document.querySelectorAll('.serving-btn').forEach(b => b.classList.remove('active'));
    const activeBtn = Array.from(document.querySelectorAll('.serving-btn'))
        .find(b => parseFloat(b.dataset.multiplier) === multiplier);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
    calculateNutrition(100 * multiplier);
}

function calculateNutrition(weightInGrams) {
    const nutrition = currentProduct.nutriments;
    const multiplier = weightInGrams / 100;
    
    const calories = nutrition['energy-kcal'] != null ? Math.round(nutrition['energy-kcal'] * multiplier) : 0;
    const carbs = nutrition['carbohydrates'] != null ? Math.round(nutrition['carbohydrates'] * multiplier) : 0;
    const protein = nutrition['proteins'] != null ? Math.round(nutrition['proteins'] * multiplier) : 0;
    const fat = nutrition['fat'] != null ? Math.round(nutrition['fat'] * multiplier) : 0;
    
    const display = document.getElementById('nutritionDisplay');
    display.innerHTML = `
        <div class="calories-container">
            <div class="main-value">${calories}</div>
            <div class="main-label">Calories</div>
        </div>
        <div class="nutrition-details">
            <div class="nutrition-item">
                <div class="nutrition-item-value">${protein}g</div>
                <div class="nutrition-item-label">Protein</div>
            </div>
            <div class="nutrition-item">
                <div class="nutrition-item-value">${carbs}g</div>
                <div class="nutrition-item-label">Carbs</div>
            </div>
            <div class="nutrition-item">
                <div class="nutrition-item-value">${fat}g</div>
                <div class="nutrition-item-label">Fat</div>
            </div>
        </div>
    `;
}

function closeCalculator() {
    document.getElementById('calculatorModal').classList.remove('active');
    document.getElementById('customWeight').value = '';
}

function showNoResults() {
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = `
        <div class="no-results">
            <i class="fa-solid fa-magnifying-glass"></i>
            <h3 style="margin-top: 16px; color: #333;">No products found</h3>
            <p style="margin-top: 8px; color: #666;">Try searching for something else</p>
        </div>
    `;
    document.getElementById('categoriesSection').classList.remove('hidden');
    document.getElementById('paginationContainer').style.display = 'none';
    
    const activeCategory = document.querySelector('.category-btn.active');
    if (!activeCategory) {
        document.getElementById('clearCategoryWrapper').style.display = 'none';
    }
    
    allProducts = [];
    currentPage = 1;
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    renderRecentSearches();
    
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            document.getElementById('searchInput').value = '';
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('loadingSpinner').classList.add('show');
            document.getElementById('results').innerHTML = '';
            searchByCategory(category);
        });
    });
    
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const query = e.target.value.trim();
        clearTimeout(searchTimeout);
        
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('clearCategoryWrapper').style.display = 'none';
        
        if (query.length >= 2) {
            document.getElementById('loadingSpinner').classList.add('show');
        } else {
            document.getElementById('loadingSpinner').classList.remove('show');
        }
        
        document.getElementById('errorMessage').style.display = 'none';
        document.getElementById('results').innerHTML = '';
        
        if (query.length === 0) {
            document.getElementById('categoriesSection').classList.remove('hidden');
            document.getElementById('paginationContainer').style.display = 'none';
            document.getElementById('clearCategoryWrapper').style.display = 'none';
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            allProducts = [];
            currentPage = 1;
            renderRecentSearches();
        }
        
        searchTimeout = setTimeout(() => {
            if (query.length >= 2) {
                searchProducts(query);
            } else if (query.length === 0) {
                document.getElementById('loadingSpinner').classList.remove('show');
            }
        }, 500);
    });
    
    const customWeightInput = document.getElementById('customWeight');
    if (customWeightInput) {
        customWeightInput.addEventListener('input', function() {
            if (currentProduct) {
                const customWeight = parseFloat(this.value);
                if (customWeight > 0) {
                    calculateNutrition(customWeight);
                    document.querySelectorAll('.serving-btn').forEach(b => b.classList.remove('active'));
                }
            }
        });
    }
    
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('calculatorModal');
        if (e.target === modal) {
            closeCalculator();
        }
    });
});

