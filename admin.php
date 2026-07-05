<?php
// ═══════════════════════════════════
//  php/admin.php
//  View all messages sent from website
//  Go to: http://localhost/aash-dev/php/admin.php
// ═══════════════════════════════════

require_once 'config.php';

// ⚠️ CHANGE THIS PASSWORD!
define('ADMIN_PASSWORD', 'aashid2025');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
    } else {
        $error = 'Wrong password!';
    }
}
if (isset($_GET['logout']))  { session_destroy(); header('Location: admin.php'); exit; }
if (isset($_GET['read'])   && isset($_SESSION['admin'])) {
    $db = getDB(); $id = (int)$_GET['read'];
    $db->query("UPDATE messages SET is_read=1 WHERE id=$id");
    $db->close(); header('Location: admin.php'); exit;
}
if (isset($_GET['delete']) && isset($_SESSION['admin'])) {
    $db = getDB(); $id = (int)$_GET['delete'];
    $db->query("DELETE FROM messages WHERE id=$id");
    $db->close(); header('Location: admin.php'); exit;
}

$messages = []; $total = 0; $unread = 0;
if (isset($_SESSION['admin'])) {
    $db = getDB();
    if ($db) {
        $r = $db->query("SELECT * FROM messages ORDER BY created_at DESC");
        while ($row = $r->fetch_assoc()) $messages[] = $row;
        $s = $db->query("SELECT COUNT(*) t, SUM(is_read=0) u FROM messages")->fetch_assoc();
        $total = $s['t']; $unread = $s['u'];
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Admin — Aash.dev</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#090910;--bg2:#111119;--bg3:#17171f;--accent:#7c6fff;--text:#f0eff8;--muted:#6b6a7e;--border:rgba(255,255,255,0.08);--card:rgba(255,255,255,0.04);--green:#1D9E75;--red:#ff6b6b}
body{background:var(--bg);color:var(--text);font-family:system-ui,sans-serif;font-size:14px;min-height:100vh}

/* LOGIN */
.login{display:flex;align-items:center;justify-content:center;min-height:100vh}
.login-box{background:var(--bg2);border:1px solid var(--border);border-radius:18px;padding:2.5rem;width:360px}
.login-box h1{font-size:1.8rem;font-weight:800;margin-bottom:.3rem}
.login-box h1 span{color:var(--accent)}
.login-box p{color:var(--muted);margin-bottom:1.5rem;font-size:13px}
.login-box input{width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:.8rem 1rem;color:var(--text);font-size:14px;outline:none;margin-bottom:1rem;transition:border-color .2s}
.login-box input:focus{border-color:var(--accent)}
.login-box button{width:100%;background:var(--accent);color:#fff;border:none;padding:.8rem;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:opacity .2s}
.login-box button:hover{opacity:.85}
.err{color:var(--red);font-size:13px;margin-bottom:.8rem}

/* PANEL */
.wrap{max-width:1100px;margin:0 auto;padding:2rem}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
.top h1{font-size:1.6rem;font-weight:800}
.top h1 span{color:var(--accent)}
.logout{background:var(--card);border:1px solid var(--border);color:var(--muted);padding:.4rem 1rem;border-radius:8px;text-decoration:none;font-size:13px;transition:color .2s}
.logout:hover{color:var(--text)}

/* STATS */
.stats{display:flex;gap:1rem;margin-bottom:2rem;flex-wrap:wrap}
.stat{background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:1.2rem 1.8rem;flex:1;min-width:120px}
.stat .n{font-size:2.2rem;font-weight:800;color:var(--accent)}
.stat .l{font-size:12px;color:var(--muted);margin-top:2px}

/* TABLE */
.tbl-wrap{background:var(--bg2);border:1px solid var(--border);border-radius:16px;overflow:auto}
table{width:100%;border-collapse:collapse}
th{background:var(--bg3);padding:.8rem 1rem;text-align:left;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);white-space:nowrap}
td{padding:.85rem 1rem;border-bottom:1px solid var(--border);vertical-align:top}
tr:last-child td{border-bottom:none}
tr.new{background:rgba(124,111,255,.04)}
tr:hover td{background:var(--card)}
.badge{display:inline-block;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;margin-left:5px;vertical-align:middle}
.msg{max-width:280px;color:var(--muted);font-size:13px;word-break:break-word}
.acts a{font-size:12px;text-decoration:none;padding:3px 10px;border-radius:6px;display:inline-block;margin-right:4px;margin-bottom:4px;white-space:nowrap}
.act-r{background:rgba(29,158,117,.15);color:var(--green)}
.act-d{background:rgba(255,107,107,.12);color:var(--red)}
.dt{font-size:11px;color:var(--muted);white-space:nowrap}
.email a{color:var(--accent);text-decoration:none;font-size:13px}
.empty{text-align:center;padding:3rem;color:var(--muted)}

/* DB STATUS */
.db-ok{display:inline-flex;align-items:center;gap:6px;background:rgba(29,158,117,.1);border:1px solid rgba(29,158,117,.25);border-radius:999px;padding:.3rem .9rem;font-size:12px;color:var(--green);margin-bottom:1.5rem}
.db-fail{display:inline-flex;align-items:center;gap:6px;background:rgba(255,107,107,.1);border:1px solid rgba(255,107,107,.25);border-radius:999px;padding:.3rem .9rem;font-size:12px;color:var(--red);margin-bottom:1.5rem}
.dot2{width:7px;height:7px;border-radius:50%;background:currentColor;animation:p 2s infinite}
@keyframes p{0%,100%{opacity:1}50%{opacity:.3}}
</style>
</head>
<body>

<?php if (!isset($_SESSION['admin'])): ?>
<div class="login">
  <div class="login-box">
    <h1>Aash<span>.dev</span></h1>
    <p>Admin panel — messages from your portfolio</p>
    <?php if (isset($error)): ?><p class="err">❌ <?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
      <input type="password" name="password" placeholder="Enter admin password" autofocus required/>
      <button type="submit">Login →</button>
    </form>
  </div>
</div>

<?php else: ?>
<div class="wrap">
  <div class="top">
    <h1>Aash<span>.dev</span> — Inbox</h1>
    <a href="?logout=1" class="logout">Logout</a>
  </div>

  <?php
    // Show DB connection status
    $testDB = getDB();
    if ($testDB) {
      echo '<div class="db-ok"><span class="dot2"></span> MySQL Connected — aashdev_portfolio</div>';
      $testDB->close();
    } else {
      echo '<div class="db-fail"><span class="dot2"></span> DB Connection Failed — check config.php</div>';
    }
  ?>

  <div class="stats">
    <div class="stat"><div class="n"><?= $total ?></div><div class="l">Total messages</div></div>
    <div class="stat"><div class="n"><?= $unread ?></div><div class="l">Unread</div></div>
    <div class="stat"><div class="n"><?= $total - $unread ?></div><div class="l">Read</div></div>
  </div>

  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Email</th><th>Subject</th>
          <th>Message</th><th>Date</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($messages)): ?>
          <tr><td colspan="7" class="empty">📭 No messages yet. Share your portfolio!</td></tr>
        <?php else: foreach ($messages as $m): ?>
          <tr class="<?= $m['is_read'] ? '' : 'new' ?>">
            <td><?= $m['id'] ?><?= !$m['is_read'] ? '<span class="badge">NEW</span>' : '' ?></td>
            <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
            <td class="email"><a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a></td>
            <td><?= htmlspecialchars($m['subject'] ?: '—') ?></td>
            <td><div class="msg"><?= htmlspecialchars(mb_substr($m['message'], 0, 100)) ?>...</div></td>
            <td><span class="dt"><?= date('d M Y', strtotime($m['created_at'])) ?><br><?= date('H:i', strtotime($m['created_at'])) ?></span></td>
            <td class="acts">
              <?php if (!$m['is_read']): ?>
                <a href="?read=<?= $m['id'] ?>" class="act-r">✓ Mark read</a>
              <?php endif; ?>
              <a href="?delete=<?= $m['id'] ?>" class="act-d" onclick="return confirm('Delete?')">✕ Delete</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
</body>
</html>