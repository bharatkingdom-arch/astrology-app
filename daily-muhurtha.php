<?php
session_start();
require 'header.php';
?>

<div class="main-container" style="min-height: 70vh; padding: 40px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h1 style="text-align: center; margin-bottom: 20px; font-family: 'Outfit', sans-serif;">Daily Muhurtha</h1>
        <p style="text-align: center; max-width: 800px; margin: 0 auto 40px; color: var(--text-color, #555);">
            Discover the most auspicious and inauspicious times of the day based on Vedic Astrology. 
            Plan your important activities during the favorable periods to ensure success and harmony.
        </p>

        <div style="background: var(--card-bg, #fff); border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 20px; color: var(--primary-color, #d35400);">Today's Muhurtha Overview</h3>
            <p style="color: var(--text-color, #666); line-height: 1.6;">
                The daily muhurtha calculation takes into account various planetary positions and their influences. 
                Our detailed engine for personalized and daily muhurtha is currently being upgraded.
                Please check back soon for live, precise muhurtha timings including Choghadiya, Rahu Kalam, and Abhijit Muhurtha tailored to your location.
            </p>
            
            <div style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div style="padding: 20px; background: rgba(46, 204, 113, 0.1); border-left: 4px solid #2ecc71; border-radius: 4px;">
                    <h4 style="color: #27ae60; margin-bottom: 10px;">Auspicious Timings</h4>
                    <ul style="list-style-type: none; padding: 0; color: var(--text-color, #444);">
                        <li style="margin-bottom: 5px;">• Abhijit Muhurtha</li>
                        <li style="margin-bottom: 5px;">• Amrit Kaal</li>
                        <li>• Brahma Muhurtha</li>
                    </ul>
                </div>
                
                <div style="padding: 20px; background: rgba(231, 76, 60, 0.1); border-left: 4px solid #e74c3c; border-radius: 4px;">
                    <h4 style="color: #c0392b; margin-bottom: 10px;">Inauspicious Timings</h4>
                    <ul style="list-style-type: none; padding: 0; color: var(--text-color, #444);">
                        <li style="margin-bottom: 5px;">• Rahu Kalam</li>
                        <li style="margin-bottom: 5px;">• Yamaganda</li>
                        <li>• Gulika Kaal</li>
                    </ul>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="<?= $BASE_URL ?>" style="display: inline-block; padding: 10px 24px; background: var(--primary-color, #e67e22); color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600;">Return Home</a>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
