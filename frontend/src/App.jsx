import React, { useState, useEffect } from 'react';
import { GlassCard } from './components/GlassCard';
import { NotificationForm } from './components/NotificationForm';
import { NotificationLogTable } from './components/NotificationLogTable';
import { SystemTerminal } from './components/SystemTerminal';
import { UserManagement } from './components/UserManagement';
import { Zap, Activity } from 'lucide-react';
import { echo } from './lib/echo';
import { Toaster, toast } from 'sonner';

function App() {
  const [isConnected, setIsConnected] = useState(false);

  useEffect(() => {
    // Listen for pusher state changes
    if (echo.connector.pusher.connection) {
      setIsConnected(echo.connector.pusher.connection.state === 'connected');
      
      echo.connector.pusher.connection.bind('state_change', (states) => {
        setIsConnected(states.current === 'connected');
      });
    }

    // Global toast listener for chaos monkey errors
    const channel = echo.channel('system-logs');
    channel.listen('.system.log', (e) => {
      if (e.level === 'ERROR') {
        toast.error(e.message, {
          style: {
            background: 'rgba(15, 23, 42, 0.9)',
            border: '1px solid rgba(239, 68, 68, 0.5)',
            color: '#f87171',
            backdropFilter: 'blur(8px)',
          }
        });
      }
    });

    return () => {
      channel.stopListening('.system.log');
    };
  }, []);

  return (
    <div className="min-h-screen bg-[#0f172a] bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-900/20 via-slate-900 to-black p-6 lg:p-12">
      <Toaster position="top-center" expand={true} />
      <div className="max-w-7xl mx-auto space-y-8">
        
        {/* Header */}
        <header className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            <div className="p-3 bg-brand-primary/20 rounded-2xl border border-brand-primary/30 shadow-[0_0_20px_rgba(59,130,246,0.2)]">
              <Zap className="text-brand-primary fill-brand-primary" size={32} />
            </div>
            <div>
              <h1 className="text-3xl font-black text-white tracking-tight">
                ESCOFFIE<span className="text-brand-primary">NEWS</span> <span className="font-light text-slate-500">NOTIFY</span>
              </h1>
              <p className="text-slate-400 text-sm font-medium flex items-center gap-2">
                <Activity size={14} className="text-emerald-500" />
                Real-time Notification Engine
              </p>
            </div>
          </div>
          
          <div className="flex items-center gap-6 text-sm">
            <div className="flex flex-col items-end">
              <span className="text-slate-500 uppercase text-[10px] font-bold tracking-widest">Environment</span>
              <span className="text-emerald-400 font-mono">
                {import.meta.env.VITE_APP_ENV || 'Production Ready'}
              </span>
            </div>
            <div className="h-8 w-px bg-slate-800" />
            <div className="flex flex-col items-end">
              <span className="text-slate-500 uppercase text-[10px] font-bold tracking-widest">Status</span>
              <span className="text-white flex items-center gap-1.5">
                <span className={`h-2 w-2 rounded-full ${isConnected ? 'bg-emerald-500 animate-pulse' : 'bg-red-500'}`} />
                {isConnected ? 'Live Sync' : 'Disconnected'}
              </span>
            </div>
          </div>
        </header>

        <main className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          {/* Left Column: Form, Terminal, Users */}
          <div className="lg:col-span-5 space-y-8">
            <GlassCard className="p-8">
              <NotificationForm />
            </GlassCard>

            <GlassCard className="p-4 bg-black/40">
              <SystemTerminal />
            </GlassCard>

            <UserManagement />
          </div>

          {/* Right Column: History — sticky, same visual height as left column */}
          <div className="lg:col-span-7">
            <div className="sticky top-8 h-[calc(100vh-8rem)]">
              <GlassCard className="p-8 h-full flex flex-col">
                <NotificationLogTable />
              </GlassCard>
            </div>
          </div>

        </main>

        <footer className="pt-8 border-t border-slate-800 flex justify-between items-center text-slate-500 text-xs">
          <p>© 2026 EscoffieNews Notification System • Coding Challenge</p>
          <div className="flex gap-4">
            <a 
              href="https://github.com/escoffie/escoffienews/blob/main/README.md" 
              target="_blank" 
              rel="noopener noreferrer"
              className="hover:text-slate-300 cursor-pointer transition-colors"
            >
              Documentation
            </a>
          </div>
        </footer>

      </div>
    </div>
  );
}

export default App;
