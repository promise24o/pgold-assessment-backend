# 🚀 Deployment Guide

## Docker Deployment

### Prerequisites
- Docker installed
- Docker Compose installed (for local testing)

---

## 🏠 Local Testing with Docker

### 1. Build and Run
```bash
docker-compose up --build
```

### 2. Access the Application
```
http://localhost:8080
```

### 3. Stop the Application
```bash
docker-compose down
```

### 4. View Logs
```bash
docker-compose logs -f
```

---

## 🌐 Deploy to Render.com

### Method 1: Using render.yaml (Recommended)

1. **Push to GitHub**
   ```bash
   git add .
   git commit -m "Add Docker configuration"
   git push origin main
   ```

2. **Connect to Render**
   - Go to https://render.com
   - Click "New +" → "Blueprint"
   - Connect your GitHub repository
   - Render will automatically detect `render.yaml`
   - Click "Apply"

3. **Done!** 🎉
   - Your app will be deployed automatically
   - You'll get a URL like: `https://pgold-backend.onrender.com`

---

### Method 2: Manual Deployment

1. **Create New Web Service**
   - Go to https://dashboard.render.com
   - Click "New +" → "Web Service"
   - Connect your GitHub repository

2. **Configure Service**
   - **Name:** pgold-backend
   - **Region:** Oregon (US West)
   - **Branch:** main
   - **Runtime:** Docker
   - **Plan:** Free

3. **Environment Variables**
   Add these in Render dashboard:
   ```
   APP_NAME=PGold
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:GENERATE_THIS_IN_RENDER
   LOG_CHANNEL=stack
   LOG_LEVEL=error
   DB_CONNECTION=sqlite
   CACHE_STORE=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   MAIL_MAILER=log
   PGOLD_API_BASE_URL=https://sandbox.pgoldapp.com
   PGOLD_CACHE_TTL=300
   ```

4. **Health Check Path**
   ```
   /up
   ```

5. **Deploy**
   - Click "Create Web Service"
   - Wait for deployment (5-10 minutes)

---

## 🔧 Post-Deployment

### 1. Generate APP_KEY
If you didn't auto-generate it, run in Render Shell:
```bash
php artisan key:generate --show
```
Then add it to environment variables.

### 2. Run Migrations
Render will automatically run migrations via `docker-entrypoint.sh`

### 3. Test the API
```bash
curl https://your-app.onrender.com/api/v1/rates/crypto
```

---

## 📊 Monitoring

### View Logs in Render
- Go to your service dashboard
- Click "Logs" tab
- Monitor real-time logs

### Health Check
Render automatically monitors `/up` endpoint

---

## 🔄 Continuous Deployment

Every push to `main` branch will automatically:
1. Build new Docker image
2. Run migrations
3. Deploy new version
4. Zero-downtime deployment

---

## 🐳 Docker Commands Reference

### Build Image
```bash
docker build -t pgold-backend .
```

### Run Container
```bash
docker run -p 8080:8080 \
  -e APP_KEY=base64:your-key-here \
  -e APP_ENV=production \
  pgold-backend
```

### View Running Containers
```bash
docker ps
```

### Stop Container
```bash
docker stop <container-id>
```

### View Logs
```bash
docker logs -f <container-id>
```

### Shell Access
```bash
docker exec -it <container-id> sh
```

---

## 🔒 Security Checklist

- ✅ `APP_DEBUG=false` in production
- ✅ Strong `APP_KEY` generated
- ✅ CORS configured properly
- ✅ Rate limiting enabled
- ✅ HTTPS enforced (Render does this automatically)
- ✅ Environment variables secured

---

## 📝 Troubleshooting

### Issue: App not starting
**Solution:** Check logs for errors
```bash
docker-compose logs app
```

### Issue: Database not found
**Solution:** Ensure SQLite file has correct permissions
```bash
chmod 664 database/database.sqlite
```

### Issue: 500 Error
**Solution:** Check storage permissions
```bash
chmod -R 775 storage bootstrap/cache
```

### Issue: Migrations not running
**Solution:** Run manually
```bash
docker exec -it pgold-backend php artisan migrate --force
```

---

## 🎯 Performance Tips

1. **Enable OPcache** (already configured in Dockerfile)
2. **Use Redis for caching** (upgrade from file cache)
3. **Enable Gzip** (already configured in nginx)
4. **Monitor with New Relic or Sentry**

---

## 📚 Additional Resources

- [Render Documentation](https://render.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [Laravel Deployment](https://laravel.com/docs/deployment)

---

## 🆘 Support

For issues:
1. Check logs first
2. Review environment variables
3. Test locally with Docker Compose
4. Contact support@pgoldapp.com
