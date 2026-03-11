<h1 align="center">半刻 · 写给自己的独白</h1>

<p align="center">
  <em>每天半刻，留文字给自己。</em><br>
  一个如旧稿纸般温润的私人写作空间 —— 可私藏，亦可分享。
</p>

<p align="center">
  <a href="https://github.com/your-username/banke/actions">
    <img src="https://img.shields.io/github/actions/workflow/status/your-username/banke/tests.yml?branch=main&label=tests&color=8B5A3C" alt="Tests">
  </a>
  <a href="https://laravel.com">
    <img src="https://img.shields.io/badge/Laravel-12.x-F8F4ED?logo=laravel&logoColor=4B3832" alt="Laravel 12">
  </a>
  <a href="LICENSE">
    <img src="https://img.shields.io/github/license/your-username/banke?color=A67C52" alt="License">
  </a>
</p>

<div align="center">
  <!-- 替换为你的真实截图 -->
  <img src="https://raw.githubusercontent.com/heese0524/laravel-project/main/screenshoots/home-page.png" width="800" alt="半刻界面预览：浅米色稿纸风格 + Markdown 编辑器">
  <img src="https://raw.githubusercontent.com/heese0524/laravel-project/main/screenshoots/profile-page.png" width="800" alt="半刻界面预览：浅米色稿纸风格 + Markdown 编辑器">
</div>

---

> _“有些话，写下来，就不怕忘记了。”_

「半刻」是一个介于日记与博客之间的写作平台。  
你可以写下只给自己看的秘密心事，也可以将思考整理成文，分享给世界。

- ✍️ **Markdown 编辑器** —— 用简洁语法，写自由文字  
- 🔒 **公开 / 私密双模式** —— 一键切换，掌控分享边界  
- 📜 **旧稿纸视觉设计** —— 浅米背景 + 深棕文字，如执笔于泛黄纸页  
- 🕊 **无干扰体验** —— 无广告、无点赞、无评论，只有你和文字  

这里不是流量场，而是你的精神角落。

---

## 🚀 快速启动

### 环境要求
- PHP 8.2+
- Composer
- MySQL / PostgreSQL / SQLite
- Node.js & npm

### 安装步骤
```bash
git clone https://github.com/your-username/banke.git
cd banke

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

# 配置数据库后运行
php artisan migrate --seed

php artisan serve
