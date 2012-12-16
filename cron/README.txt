Note: You will need to change the first line of all cron jobs to the correct location.

Cron Settings:

+-------------------+---------------------+
| File Name         | CRON Setting        |
+-------------------+---------------------+
| xmail_clear.php   | */15 * * * *        |
+-------------------+---------------------+
| xmail_snail.php   | 0 */2 * * *         |
+-------------------+---------------------+


xmail_clear.php - Generic cleanup file
xmail_snail.php - Snail mail handler