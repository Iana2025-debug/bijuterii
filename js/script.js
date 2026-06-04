document.addEventListener('DOMContentLoaded',()=>{
  // simple form handlers
  const loginForm=document.getElementById('loginForm');
  const registerForm=document.getElementById('registerForm');
  const contactForm=document.getElementById('contactForm');

  if(loginForm){
    loginForm.addEventListener('submit', async e=>{
      e.preventDefault();
      const email = document.getElementById('loginEmail').value.trim();
      const password = document.getElementById('loginPassword').value;
      const msgEl = document.getElementById('loginMessage');
      msgEl.textContent = '';
      try{
        const res = await fetch('/api/login',{ method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ email, password }) });
        const j = await res.json().catch(()=>({}));
        if(res.ok){
          // redirect to dashboard
          window.location.href = 'dashboard.html';
        }else{
          msgEl.textContent = j && j.error ? j.error : 'Login failed.';
        }
      }catch(err){
        console.error(err);
        msgEl.textContent = 'Network error — could not login.';
      }
    });
  }

  if(registerForm){
    registerForm.addEventListener('submit', async e=>{
      e.preventDefault();
      const name=document.getElementById('name').value.trim();
      const email=document.getElementById('email').value.trim();
      const password=document.getElementById('password').value;
      const confirmPassword=document.getElementById('confirmPassword').value;
      const msgEl=document.getElementById('registerMessage');

      msgEl.textContent='';
      if(!name || !email || !password){
        msgEl.textContent='Please complete all fields.';
        return;
      }
      if(password!==confirmPassword){
        msgEl.textContent='Passwords do not match.';
        return;
      }

      try{
        const res=await fetch('/api/register',{headers:{'Content-Type':'application/json'},method:'POST',body:JSON.stringify({name,email,password})});
        const j=await res.json();
        if(res.ok){
          msgEl.textContent='Registration successful — you can now log in.';
          registerForm.reset();
        }else{
          msgEl.textContent = j && j.error ? j.error : 'Registration failed.';
        }
      }catch(err){
        console.error(err);
        msgEl.textContent='Network error — could not register.';
      }
    });
  }

  if(contactForm){
    contactForm.addEventListener('submit',e=>{
      e.preventDefault();
      alert('Message sent (demo)');
    });
  }

  // load sample items on dashboard
  const itemsEl=document.getElementById('items');
  if(itemsEl){
    fetch('items.json').then(r=>r.json()).then(data=>{
      data.forEach(it=>{
        const d=document.createElement('div');
        d.className='item';
        d.innerHTML=`<h4>${it.name}</h4><p>${it.description}</p><strong>${it.price}</strong>`;
        itemsEl.appendChild(d);
      });
    }).catch(()=>{itemsEl.innerHTML='<p>No items found.</p>'});
  }

  // Dashboard: protected area — check session and load users
  const dashboardUserEl = document.getElementById('dashboardUser');
  if(dashboardUserEl){
    (async ()=>{
      try{
        const me = await fetch('/api/me');
        if(me.status === 204){
          window.location.href = 'login.html';
          return;
        }
        if(!me.ok) throw new Error('Not authenticated');
        const mj = await me.json();
        const user = mj.user;
        dashboardUserEl.innerHTML = `<strong>Bine ai venit, ${user.name}</strong> (<span style="font-size:0.9rem">${user.email}</span>)`;

        // load users list
        const usersRes = await fetch('/api/users');
        const usersWrap = document.getElementById('usersList');
        if(!usersRes.ok){
          usersWrap.innerText = 'Could not load users.';
          return;
        }
        const uj = await usersRes.json();
        const users = uj.users || [];
        if(users.length===0){ usersWrap.innerText='No users found.'; return; }
        const t = document.createElement('table');
        t.style.width='100%';
        t.style.borderCollapse='collapse';
        t.innerHTML = `<thead><tr><th style="text-align:left;padding:8px">ID</th><th style="text-align:left;padding:8px">Name</th><th style="text-align:left;padding:8px">Email</th></tr></thead>`;
        const tb = document.createElement('tbody');
        users.forEach(u=>{
          const tr = document.createElement('tr');
          tr.innerHTML = `<td style="padding:8px;border-top:1px solid #eee">${u.id}</td><td style="padding:8px;border-top:1px solid #eee">${u.name}</td><td style="padding:8px;border-top:1px solid #eee">${u.email}</td>`;
          tb.appendChild(tr);
        });
        t.appendChild(tb);
        usersWrap.innerHTML='';
        usersWrap.appendChild(t);

        // logout
        const logoutBtn = document.getElementById('logoutBtn');
        if(logoutBtn){
          logoutBtn.addEventListener('click', async ()=>{
            await fetch('/api/logout',{method:'POST'}).catch(()=>{});
            window.location.href='login.html';
          });
        }

      }catch(err){
        console.error(err);
        window.location.href='login.html';
      }
    })();
  }
});