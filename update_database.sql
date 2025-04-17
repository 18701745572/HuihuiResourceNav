-- 为categories表添加user_id字段
ALTER TABLE categories ADD COLUMN user_id INT NOT NULL DEFAULT 1;

-- 为mylink表添加user_id字段
ALTER TABLE mylink ADD COLUMN user_id INT NOT NULL DEFAULT 1;

-- 添加外键约束
ALTER TABLE categories ADD FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE mylink ADD FOREIGN KEY (user_id) REFERENCES users(id);

-- 更新现有数据
UPDATE categories SET user_id = 1 WHERE user_id = 1;
UPDATE mylink SET user_id = 1 WHERE user_id = 1; 