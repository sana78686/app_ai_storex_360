# Clear all Laravel caches
Write-Host "Clearing Laravel caches..." -ForegroundColor Yellow

# Clear view cache
php artisan view:clear
Write-Host "✓ View cache cleared" -ForegroundColor Green

# Clear config cache
php artisan config:clear
Write-Host "✓ Config cache cleared" -ForegroundColor Green

# Clear route cache
php artisan route:clear
Write-Host "✓ Route cache cleared" -ForegroundColor Green

# Clear application cache (if using file cache)
php artisan cache:clear 2>$null
Write-Host "✓ Application cache cleared" -ForegroundColor Green

# Remove compiled views manually
if (Test-Path "storage\framework\views") {
    Get-ChildItem "storage\framework\views" -Filter "*.php" | Remove-Item -Force -ErrorAction SilentlyContinue
    Write-Host "✓ Compiled views removed" -ForegroundColor Green
}

# Clear bootstrap cache (will be regenerated)
if (Test-Path "bootstrap\cache") {
    Get-ChildItem "bootstrap\cache" -Filter "*.php" | Remove-Item -Force -ErrorAction SilentlyContinue
    Write-Host "✓ Bootstrap cache cleared" -ForegroundColor Green
}

Write-Host "`nAll caches cleared successfully!" -ForegroundColor Green
Write-Host "Please restart your Laravel development server." -ForegroundColor Cyan
