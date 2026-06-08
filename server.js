const express = require('express');
const path = require('path');
const fs = require('fs').promises;
const bcrypt = require('bcryptjs');
const session = require('express-session');

const app = express();
const PORT = process.env.PORT || 3000;
const USERS_FILE = path.join(__dirname, 'users.json');
const ORDERS_FILE = path.join(__dirname, 'orders.json');

app.use(express.json());
app.use(express.static(path.join(__dirname)));

// Redirect root to shop page when using Node static server
app.get('/', (req, res) => {
  res.redirect('/bijuterii.html');
});

app.use(session({
  secret: process.env.SESSION_SECRET || 'devsecret',
  resave: false,
  saveUninitialized: false,
  cookie: { maxAge: 24 * 60 * 60 * 1000 }
}));

async function readUsers(){
  try{
    const txt = await fs.readFile(USERS_FILE, 'utf8');
    return JSON.parse(txt);
  }catch(e){
    return [];
  }
}

async function writeUsers(users){
  await fs.writeFile(USERS_FILE, JSON.stringify(users, null, 2), 'utf8');
}

async function readOrders(){
  try{
    const txt = await fs.readFile(ORDERS_FILE, 'utf8');
    return JSON.parse(txt);
  }catch(e){
    return [];
  }
}

async function writeOrders(orders){
  await fs.writeFile(ORDERS_FILE, JSON.stringify(orders, null, 2), 'utf8');
}

// Accept an order payload and persist to orders.json (demo: mask card data server-side)
app.post('/api/orders', async (req, res) => {
  const { items, total, payment, customer } = req.body || {};
  if(!items || !Array.isArray(items) || items.length === 0) return res.status(400).json({ error: 'No items in order.' });
  if(!payment || !payment.cardHolder || !payment.last4) return res.status(400).json({ error: 'Missing payment info.' });
  try{
    const orders = await readOrders();
    const nextId = orders.reduce((m, o) => Math.max(m, o.id || 0), 0) + 1;
    const order = {
      id: nextId,
      items,
      total,
      payment: {
        cardHolder: payment.cardHolder,
        last4: payment.last4,
        expiry: payment.expiry || null
      },
      customer: (req.session && req.session.user) ? req.session.user : (customer || null),
      createdAt: new Date().toISOString()
    };
    orders.push(order);
    await writeOrders(orders);
    res.status(201).json({ orderId: nextId, message: 'Comanda a fost înregistrată.' });
  }catch(err){
    console.error(err);
    res.status(500).json({ error: 'Server error' });
  }
});

app.post('/api/register', async (req, res) => {
  const {name, email, password} = req.body || {};
  if(!name || !email || !password){
    return res.status(400).json({error: 'Missing required fields.'});
  }

  try{
    const users = await readUsers();
    const exists = users.find(u=>u.email && u.email.toLowerCase()===email.toLowerCase());
    if(exists){
      return res.status(409).json({error: 'Email already registered.'});
    }

    const hash = await bcrypt.hash(password, 10);
    const nextId = users.reduce((m,u)=> Math.max(m, u.id || 0), 0) + 1;
    const newUser = { id: nextId, name, email, passwordHash: hash };
    users.push(newUser);
    await writeUsers(users);
    const safe = { id: newUser.id, name: newUser.name, email: newUser.email };
    // set session on register
    req.session.user = safe;
    res.status(201).json({user: safe});
  }catch(err){
    console.error(err);
    res.status(500).json({error: 'Server error'});
  }
});

// Login endpoint
app.post('/api/login', async (req, res) => {
  const { email, password } = req.body || {};
  if(!email || !password) return res.status(400).json({ error: 'Missing email or password.' });
  try{
    const users = await readUsers();
    const user = users.find(u=>u.email && u.email.toLowerCase() === String(email).toLowerCase());
    if(!user || !user.passwordHash) return res.status(401).json({ error: 'Invalid credentials.' });
    const ok = await bcrypt.compare(password, user.passwordHash);
    if(!ok) return res.status(401).json({ error: 'Invalid credentials.' });
    const safe = { id: user.id, name: user.name, email: user.email };
    req.session.user = safe;
    res.json({ user: safe });
  }catch(err){
    console.error(err);
    res.status(500).json({ error: 'Server error' });
  }
});

// Who am I
app.get('/api/me', (req, res) => {
  if(req.session && req.session.user) return res.json({ user: req.session.user });
  return res.status(204).end();
});

// Logout
app.post('/api/logout', (req, res) => {
  if(req.session){
    req.session.destroy(err=>{
      if(err){ console.error(err); return res.status(500).json({ error: 'Logout failed' }); }
      res.json({ ok: true });
    });
  }else res.json({ ok: true });
});

// Protected: list users (no password hashes)
app.get('/api/users', async (req, res) => {
  if(!req.session || !req.session.user) return res.status(401).json({ error: 'Unauthorized' });
  try{
    const users = await readUsers();
    const safe = users.map(u=>({ id: u.id, name: u.name, email: u.email }));
    res.json({ users: safe });
  }catch(err){
    console.error(err);
    res.status(500).json({ error: 'Server error' });
  }
});

app.listen(PORT, ()=>{
  console.log(`Server running on http://localhost:${PORT}`);
});
