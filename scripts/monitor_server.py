#!/usr/bin/env python3
"""
Python Monitor Script para Dev Manager
Instale em cada servidor para enviar métricas em tempo real.

Uso:
    python3 monitor_server.py --server-ip IP --api-url URL [--interval SEGUNDOS]

Para rodar em background:
    nohup python3 monitor_server.py --server-ip 192.168.1.100 --api-url https://devmanager.com/api/server/update &
"""

import argparse
import json
import os
import sys
import time
import subprocess
import requests
from datetime import datetime

try:
    import psutil
except ImportError:
    print("Erro: instale psutil com: pip install psutil")
    sys.exit(1)


def get_git_info(path):
    """Pegar informações do git"""
    try:
        os.chdir(path)
        
        branch = subprocess.check_output(['git', 'branch', '--show-current'], text=True).strip()
        commit = subprocess.check_output(['git', 'rev-parse', 'HEAD'], text=True).strip()[:8]
        
        return branch, commit
    except:
        return 'main', ''


def get_system_metrics():
    """Pegar métricas do sistema"""
    return {
        'cpu': psutil.cpu_percent(interval=1),
        'ram': psutil.virtual_memory().percent,
        'disk': psutil.disk_usage('/').percent,
    }


def send_metrics(api_url, ip, branch, commit, version):
    """Enviar métricas para o Laravel"""
    metrics = get_system_metrics()
    
    data = {
        'ip_address': ip,
        'cpu': metrics['cpu'],
        'ram': metrics['ram'],
        'disk': metrics['disk'],
        'branch': branch,
        'commit': commit,
        'version': version,
        'status': 'online',
        'timestamp': datetime.now().isoformat(),
    }
    
    try:
        response = requests.post(api_url, json=data, timeout=10)
        if response.status_code == 200:
            print(f"✓ Métricas enviadas: CPU={data['cpu']}%, RAM={data['ram']}%, DISK={data['disk']}%")
            return True
        else:
            print(f"✗ Erro ao enviar: {response.status_code}")
            return False
    except requests.exceptions.RequestException as e:
        print(f"✗ Erro de conexão: {e}")
        return False


def main():
    parser = argparse.ArgumentParser(description='Monitor de servidor para Dev Manager')
    parser.add_argument('--server-ip', required=True, help='IP do servidor')
    parser.add_argument('--api-url', required=True, help='URL da API do Laravel')
    parser.add_argument('--project-path', default='/var/www/html', help='Caminho do projeto')
    parser.add_argument('--project-version', default='', help='Versão do projeto')
    parser.add_argument('--interval', type=int, default=30, help='Intervalo em segundos (padrão: 30)')
    
    args = parser.parse_args()
    
    print(f"🚀 Monitor iniciado para {args.server_ip}")
    print(f"📡 Enviando para: {args.api_url}")
    print(f"⏱️  Intervalo: {args.interval}s")
    print("-" * 40)
    
    branch, commit = get_git_info(args.project_path)
    print(f"📦 Branch: {branch}, Commit: {commit}")
    
    while True:
        success = send_metrics(
            args.api_url,
            args.server_ip,
            branch,
            commit,
            args.project_version
        )
        
        if success:
            time.sleep(args.interval)
        else:
            time.sleep(args.interval * 2)


if __name__ == '__main__':
    main()