import React from 'react';
import { GlassCard } from './components/GlassCard';
import { NotificationForm } from './components/NotificationForm';
import { NotificationLogTable } from './components/NotificationLogTable';
import { SystemTerminal } from './components/SystemTerminal';
import { Zap, Activity } from 'lucide-react';

function App() {
  return (
    <div className="min-h-screen bg-[#0f172a] bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-900/20 via-slate-900 to-black p-6 lg:p-12">
      <div className="max-w-7xl mx-auto space-y-8">
        
        {/* Header */}
        <header className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            <div className="p-3 bg-brand-primary/20 rounded-2xl border border-brand-primary/30 shadow-[0_0_20px_rgba(59,130,246,0.2)]">
              <Zap className="text-brand-primary fill-brand-primary" size={32} />
            </div>
            <div>
              <h1 className="text-3xl font-black text-white tracking-tight">
                LOAN<span className="text-brand-primary">PRO</span> <span className="font-light text-slate-500">NOTIFY</span>
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
              <span className="text-emerald-400 font-mono">Production Ready</span>
            </div>
            <div className="h-8 w-px bg-slate-800" />
            <div className="flex flex-col items-end">
              <span className="text-slate-500 uppercase text-[10px] font-bold tracking-widest">Status</span>
              <span className="text-white flex items-center gap-1.5">
                <span className="h-2 w-2 rounded-full bg-emerald-500 animate-pulse" />
                Live Sync
              </span>
            </div>
          </div>
        </header>

        <main className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          {/* Left Column: Form & Terminal */}
          <div className="lg:col-span-5 space-y-8">
            <GlassCard className="p-8">
              <NotificationForm />
            </GlassCard>

            <GlassCard className="p-4 bg-black/40">
              <SystemTerminal />
            </GlassCard>
          </div>

          {/* Right Column: History */}
          <div className="lg:col-span-7 h-[calc(100vh-200px)] min-h-[600px]">
            <GlassCard className="p-8 h-full">
              <NotificationLogTable />
            </GlassCard>
          </div>

        </main>

        <footer className="pt-8 border-t border-slate-800 flex justify-between items-center text-slate-500 text-xs">
          <p>© 2026 LoanPro Notification System • Coding Challenge</p>
          <div className="flex gap-4">
            <span className="hover:text-slate-300 cursor-pointer transition-colors">Documentation</span>
            <span className="hover:text-slate-300 cursor-pointer transition-colors">Support</span>
          </div>
        </footer>

      </div>
    </div>
  );
}

export default App;
