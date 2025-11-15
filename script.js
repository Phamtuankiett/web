function showModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeModal() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(m => m.style.display = 'none');
}

function editKey(key) {
    document.getElementById('edit_id').value = key.id;
    document.getElementById('edit_name').value = key.key_name;
    document.getElementById('edit_tag').value = key.tag_name;
    document.getElementById('edit_spam').value = key.spam_size;
    
    const date = new Date(key.expires_at);
    document.getElementById('edit_day').value = date.getDate();
    document.getElementById('edit_month').value = date.getMonth() + 1;
    document.getElementById('edit_year').value = date.getFullYear();
    
    showModal('editModal');
}

function deleteKey(id) {
    if (confirm('Delete this key?')) {
        window.location.href = 'actions.php?action=delete&id=' + id;
    }
}

function showCode(key) {
    const domain = window.location.origin;
    const url = `${domain}/key.php?c=${key.key_code}`;
    const tag = key.tag_name;
    
    const code = `def chk_lic():
    try:
        import requests
        import re
        
        user_key = input("Enter Key: ").strip()
        
        url = "${url}"
        
        response = requests.get(url, stream=True, timeout=15)
        
        if response.status_code != 200:
            print("Cannot connect to authentication server!")
            sys.exit(1)
        
        buffer = ""
        for chunk in response.iter_content(chunk_size=4096, decode_unicode=True):
            if chunk:
                buffer += chunk
                
                match = re.search(r'<${tag}>([^<]+)</${tag}>', buffer)
                if match:
                    response.close()
                    server_key = match.group(1).strip()
                    
                    if user_key == server_key:
                        return True
                    else:
                        print("Invalid key")
                        sys.exit(1)
                
                if len(buffer) > 50000:
                    buffer = buffer[-10000:]
        
        print("Key not found on server!")
        sys.exit(1)
        
    except Exception as e:
        print(f"Authentication error: {e}")
        sys.exit(1)`;
    
    document.getElementById('pyCode').textContent = code;
    showModal('codeModal');
}

function copyCode() {
    const code = document.getElementById('pyCode').textContent;
    navigator.clipboard.writeText(code).then(() => {
        alert('Code Copied To Clipboard!');
    });
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        closeModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-3px)';
    });
    
    btn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

const cards = document.querySelectorAll('.key-card');
cards.forEach((card, index) => {
    card.style.animationDelay = (index * 0.1) + 's';
});

if (document.querySelector('.particles')) {
    const particles = document.querySelector('.particles');
    for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.style.position = 'absolute';
        particle.style.width = Math.random() * 3 + 'px';
        particle.style.height = particle.style.width;
        particle.style.background = 'rgba(74, 158, 255, 0.4)';
        particle.style.borderRadius = '50%';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animation = `float ${Math.random() * 10 + 10}s infinite`;
        particle.style.animationDelay = Math.random() * 5 + 's';
        particles.appendChild(particle);
    }
}

const style = document.createElement('style');
style.textContent = `
    @keyframes float {
        0%, 100% {
            transform: translate(0, 0);
            opacity: 0.3;
        }
        50% {
            transform: translate(${Math.random() * 100 - 50}px, ${Math.random() * 100 - 50}px);
            opacity: 0.6;
        }
    }
`;
document.head.appendChild(style);