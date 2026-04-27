import React, { useEffect, useRef, useState } from 'react';
import { Terminal as TerminalIcon, Circle } from 'lucide-react';
import { echo } from '../lib/echo';

export const SystemTerminal = () => {
    const [logs, setLogs] = useState([]);
    const scrollRef = useRef(null);

    useEffect(() => {
        const channel = echo.channel('system-logs')
            .listen('.system.log', (e) => {
                const timestamp = new Date().toLocaleTimeString();
                setLogs(prev => [...prev, { ...e, timestamp }].slice(-100));
            });

        return () => {
            channel.stopListening('.system.log');
        };
    }, []);

    useEffect(() => {
        if (scrollRef.current) {
            scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
        }
    }, [logs]);

    const clearLogs = () => setLogs([]);

    return (
        <div className="flex flex-col h-[300px] bg-black/80 rounded-lg border border-slate-700 font-mono text-xs overflow-hidden shadow-inner">
            <div className="flex items-center justify-between px-3 py-2 bg-slate-800/50 border-b border-slate-700">
                <div className="flex items-center gap-2">
                    <TerminalIcon size={14} className="text-emerald-400" />
                    <span className="text-slate-400 uppercase tracking-wider font-bold">System Trace</span>
                </div>
                <div className="flex items-center gap-3">
                    {logs.length > 0 && (
                        <button 
                            onClick={clearLogs}
                            className="text-[10px] text-slate-500 hover:text-slate-300 transition-colors uppercase font-bold"
                        >
                            Clear
                        </button>
                    )}
                    <div className="flex gap-1.5">
                        <Circle size={8} className="fill-red-500 text-red-500" />
                        <Circle size={8} className="fill-amber-500 text-amber-500" />
                        <Circle size={8} className="fill-emerald-500 text-emerald-500" />
                    </div>
                </div>
            </div>
            <div 
                ref={scrollRef}
                className="flex-1 p-3 overflow-y-auto space-y-1 scrollbar-thin scrollbar-thumb-slate-700"
            >
                {logs.length === 0 && (
                    <div className="text-slate-600 animate-pulse italic">Waiting for system events...</div>
                )}
                {logs.map((log, index) => (
                    <div key={index} className="flex gap-2">
                        <span className="text-slate-500">[{log.timestamp}]</span>
                        <span className={clsx(
                            "font-bold",
                            log.level === 'INFO' ? 'text-blue-400' : 'text-red-400'
                        )}>{log.level}:</span>
                        <span className="text-slate-300">{log.message}</span>
                    </div>
                ))}
            </div>
        </div>
    );
};

const clsx = (...args) => args.filter(Boolean).join(' ');
