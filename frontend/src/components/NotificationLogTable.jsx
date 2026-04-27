import React, { useEffect, useState } from 'react';
import { History, Mail, MessageSquare, Bell, Clock, Trash2 } from 'lucide-react';
import { echo } from '../lib/echo';
import api from '../lib/api';
import { motion, AnimatePresence } from 'framer-motion';

export const NotificationLogTable = () => {
    const [logs, setLogs] = useState([]);
    const [isClearing, setIsClearing] = useState(false);

    useEffect(() => {
        // Initial fetch
        api.get('/logs')
           .then(res => setLogs(res.data))
           .catch(err => console.error('Failed to fetch initial logs', err));

        // Listen for new logs
        const channel = echo.channel('notifications')
            .listen('.notification.logged', (e) => {
                setLogs(prev => [e.log, ...prev].slice(0, 100)); // Cap at 100
            });

        return () => {
            channel.stopListening('.notification.logged');
        };
    }, []);

    const clearLogs = async () => {
        if (!confirm('Are you sure you want to clear all notification logs?')) return;
        
        setIsClearing(true);
        try {
            await api.delete('/logs');
            setLogs([]);
        } catch (err) {
            console.error('Failed to clear logs', err);
        } finally {
            setIsClearing(false);
        }
    };

    const getIcon = (channel) => {
        const channelLower = channel?.toLowerCase() || '';
        if (channelLower.includes('sms')) return <MessageSquare size={16} className="text-emerald-400 shrink-0" />;
        if (channelLower.includes('mail')) return <Mail size={16} className="text-blue-400 shrink-0" />;
        if (channelLower.includes('push')) return <Bell size={16} className="text-amber-400 shrink-0" />;
        return <Bell size={16} className="text-slate-400 shrink-0" />;
    };

    return (
        <div className="flex flex-col h-full relative">
            <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                    <History className="text-brand-primary shrink-0" size={20} />
                    <h2 className="text-xl font-bold text-slate-200">Notification History</h2>
                </div>
                {logs.length > 0 && (
                    <button
                        onClick={clearLogs}
                        disabled={isClearing}
                        className="flex items-center gap-1.5 px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold rounded-lg border border-red-500/20 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed group"
                    >
                        <Trash2 size={14} className="group-hover:scale-110 transition-transform" />
                        {isClearing ? 'Clearing...' : 'Clear History'}
                    </button>
                )}
            </div>
            
            <div className="flex-1 overflow-y-auto pr-2 custom-scrollbar relative">
                <table className="w-full text-left border-separate border-spacing-y-2 table-fixed">
                    <thead>
                        <tr className="text-slate-500 text-xs uppercase tracking-widest">
                            <th className="px-4 py-2 font-medium w-1/4">User</th>
                            <th className="px-4 py-2 font-medium w-1/6">Category</th>
                            <th className="px-4 py-2 font-medium w-1/5">Channel</th>
                            <th className="px-4 py-2 font-medium w-auto">Message</th>
                            <th className="px-4 py-2 font-medium w-24">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <AnimatePresence initial={false}>
                            {logs.map((log) => (
                                <motion.tr 
                                    key={log.id}
                                    initial={{ opacity: 0, x: -20 }}
                                    animate={{ opacity: 1, x: 0 }}
                                    className="bg-slate-800/30 hover:bg-slate-800/60 transition-colors group rounded-lg"
                                >
                                    <td className="px-4 py-4 rounded-l-lg border-y border-l border-slate-700/50">
                                        <div className="font-medium text-slate-200">{log.user_name}</div>
                                        <div className="text-xs text-slate-500">{log.user_email}</div>
                                    </td>
                                    <td className="px-4 py-4 border-y border-slate-700/50">
                                        <span className="px-2 py-1 bg-slate-700/50 rounded text-[10px] font-bold uppercase text-slate-300">
                                            {log.category}
                                        </span>
                                    </td>
                                    <td className="px-4 py-4 border-y border-slate-700/50">
                                        <div className="flex items-center gap-2 text-sm text-slate-300 truncate">
                                            {getIcon(log.channel)}
                                            <span className="truncate">{log.channel}</span>
                                        </div>
                                    </td>
                                    <td className="px-4 py-4 border-y border-slate-700/50">
                                        <p className="text-sm text-slate-400 break-words line-clamp-2 hover:line-clamp-none transition-all duration-300">
                                            {log.message}
                                        </p>
                                    </td>
                                    <td className="px-4 py-4 rounded-r-lg border-y border-r border-slate-700/50 text-xs text-slate-500 whitespace-nowrap">
                                        {new Date(log.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                    </td>
                                </motion.tr>
                            ))}
                        </AnimatePresence>
                    </tbody>
                </table>
                {logs.length === 0 && (
                    <div className="text-center py-12 text-slate-600">No logs found.</div>
                )}
            </div>

            {/* Bottom Blur Overlay for Scrolling Indication */}
            <div className="absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-slate-900/90 to-transparent pointer-events-none rounded-b-xl" />
        </div>
    );
};
