-- ============================================================
--  BABYBLISS — PILLAR RESTRUCTURE MIGRATION
--  Adds the 3 core pillars required by the problem statement:
--  Baby Gear · Nutrition · Parenting
--  Run: mysql -u root -p babybliss_marketplace < pillars_migration.sql
-- ============================================================

USE babybliss_marketplace;

-- ── 1. Add pillar field to categories ──────────────────────
-- Every category now belongs to one of the 3 pillars.
ALTER TABLE categories
  ADD COLUMN pillar ENUM('baby_gear','nutrition','parenting')
  NOT NULL DEFAULT 'baby_gear' AFTER icon;

-- All existing categories (toys, puzzles, etc.) become Baby Gear
UPDATE categories SET pillar = 'baby_gear';

-- ── 2. New Nutrition categories ─────────────────────────────
INSERT INTO categories (name, icon, pillar, is_active) VALUES
  ('Infant Formula & Milk',          '🍼', 'nutrition', 1),
  ('Vitamins & Supplements',         '💊', 'nutrition', 1),
  ('Baby Food & Snacks',             '🥣', 'nutrition', 1),
  ('Feeding Bottles & Accessories',  '🍽️', 'nutrition', 1),
  ('Breastfeeding Essentials',       '🤱', 'nutrition', 1);

-- ── 3. New Parenting categories ─────────────────────────────
INSERT INTO categories (name, icon, pillar, is_active) VALUES
  ('Baby Monitors & Safety',  '📹', 'parenting', 1),
  ('Parenting Books & Guides','📚', 'parenting', 1),
  ('Maternity Care',          '🤰', 'parenting', 1),
  ('Sleep & Comfort',         '😴', 'parenting', 1),
  ('Parenting Courses',       '🎓', 'parenting', 1);

-- ── 4. Articles table (used by Nutrition & Parenting) ───────
-- Holds the "guidance" content required by the problem statement.
CREATE TABLE IF NOT EXISTS articles (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  pillar        ENUM('nutrition','parenting') NOT NULL,
  topic         VARCHAR(100)  DEFAULT NULL,        -- e.g. "Feeding", "Sleep"
  title         VARCHAR(255)  NOT NULL,
  slug          VARCHAR(255)  NOT NULL UNIQUE,
  icon          VARCHAR(10)   DEFAULT '📖',
  excerpt       TEXT,
  content       LONGTEXT      NOT NULL,
  author        VARCHAR(100)  DEFAULT 'BabyBliss Team',
  read_time     INT           DEFAULT 5,            -- minutes
  views         INT           DEFAULT 0,
  is_published  TINYINT(1)    DEFAULT 1,
  created_at    DATETIME      DEFAULT CURRENT_TIMESTAMP
);

-- ── 5. Sample Nutrition articles ────────────────────────────
INSERT INTO articles (pillar, topic, title, slug, icon, excerpt, content, read_time) VALUES
('nutrition','Feeding','Breastfeeding vs Formula: Making the Right Choice','breastfeeding-vs-formula','🤱',
 'Both options can nourish your baby well — here is how to decide what works best for your family.',
 'Breastfeeding and formula feeding both provide the nutrition your baby needs to grow. Breast milk offers natural antibodies and adjusts to your baby''s changing needs, while formula gives you flexibility and lets other caregivers share feeding duties. The right choice depends on your health, lifestyle, and comfort — and many parents successfully combine both. Talk to your pediatrician about what fits your situation best, and remember: a fed baby is a happy baby, however that happens.',
 6),
('nutrition','Weaning','Introducing Solid Foods: A Month-by-Month Guide','introducing-solid-foods','🥣',
 'From first purees to finger foods — a practical timeline for starting solids safely.',
 'Most babies are ready for solid foods around 6 months. Start with single-ingredient purees like rice cereal, mashed banana, or sweet potato, introducing one new food every 3-4 days to watch for allergies. By 8-9 months, move to soft mashed or finely chopped foods. By 12 months, most babies can handle small soft chunks and finger foods. Always supervise meals and avoid honey, whole nuts, and choking hazards under age 1.',
 8),
('nutrition','Supplements','Top 10 Iron-Rich Foods for Growing Toddlers','iron-rich-foods-toddlers','🥦',
 'Iron deficiency is common in toddlers. These everyday foods can help keep levels healthy.',
 'Iron is essential for brain development and energy. Great toddler-friendly sources include: fortified cereals, pureed meats, lentils, beans, spinach, eggs, tofu, raisins, sweet potatoes, and fortified pasta. Pair iron-rich foods with vitamin C (like orange slices or strawberries) to boost absorption. If you''re concerned about your toddler''s iron levels, ask your pediatrician about a simple blood test.',
 5),
('nutrition','Hydration','How Much Water Does Your Baby Really Need?','baby-hydration-guide','💧',
 'Hydration needs change fast in the first two years — here is what matters at each stage.',
 'Babies under 6 months get all the hydration they need from breast milk or formula — extra water isn''t necessary and can even be risky. Once solids start around 6 months, small sips of water with meals are fine. By 12 months, toddlers can drink water freely throughout the day, aiming for roughly 1-4 cups depending on activity and climate. Watch for signs of dehydration like fewer wet diapers or dry lips, especially in hot weather.',
 4);

-- ── 6. Sample Parenting articles ────────────────────────────
INSERT INTO articles (pillar, topic, title, slug, icon, excerpt, content, read_time) VALUES
('parenting','Sleep','Newborn Sleep Schedule: What to Expect','newborn-sleep-schedule','😴',
 'Sleep patterns in the first 3 months can feel chaotic — here is the science behind it.',
 'Newborns sleep 14-17 hours a day but rarely for more than 2-4 hours at a stretch, since their tiny stomachs need frequent feeding. Day-night confusion is normal until around 6-8 weeks, when natural circadian rhythms start to develop. To help, keep nights dark and quiet, expose your baby to daylight during the day, and follow a simple bedtime routine. By 3-4 months, many babies begin sleeping in longer stretches — though every baby is different.',
 7),
('parenting','Behavior','Managing Toddler Tantrums: A Survival Guide','managing-toddler-tantrums','😤',
 'Practical, calm strategies for the hardest moments of toddlerhood.',
 'Tantrums happen because toddlers feel big emotions without the words or skills to express them. Stay calm, get on their level, and name the feeling ("You''re really frustrated"). Avoid giving in to demands during a meltdown, but offer comfort once they''ve calmed. Prevention helps too: keep routines predictable, offer choices when possible, and watch for hunger or tiredness triggers. Tantrums usually peak around age 2-3 and fade as language skills grow.',
 6),
('parenting','Safety','Choosing the Right Baby Monitor for Your Home','choosing-baby-monitor','📹',
 'Video, audio, or smart monitor? Here is how to decide.',
 'Audio monitors are simple and affordable, ideal for smaller homes. Video monitors let you see your baby without entering the room, great for checking sleep position or wakefulness. Smart monitors track breathing, movement, or sleep patterns — helpful for extra peace of mind but not a replacement for safe sleep practices. Whichever you choose, place it at least 3 feet from the crib and keep cords completely out of reach.',
 5),
('parenting','Development','Milestones: What to Expect in Your Baby First Year','first-year-milestones','🌱',
 'A month-by-month look at physical, social, and language development.',
 'By 3 months, babies typically smile socially and hold their head up briefly. By 6 months, many can sit with support and babble. By 9 months, crawling and pulling to stand are common, along with simple gestures like waving. By 12 months, many babies say a first word or two and may take early steps. Remember: these are averages, not deadlines — every baby develops at their own pace, and a wide range is perfectly normal.',
 9);
