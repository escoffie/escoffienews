import React, { useEffect, useState } from 'react';
import { History, Mail, MessageSquare, Bell, Clock } from 'lucide-react';
import { echo } from '../lib/echo';
import api from '../lib/api';
import { motion, AnimatePresence } from 'framer-motion';

export const NotificationLogTable = () => {
    const [logs, setLogs] = useState([]);

    useEffect(() => {
        // Initial fetch
        api.get('/logs').then(res => setLogs(res.data));

        // Listen for new logs
        const channel = echo.channel('notifications')
            .listen('.notification.logged', (e) => {
                setLogs(prev => [e.log, ...prev]);
            });

        return () => {
            channel.stopListening('.notification.logged');
        };
    }, []);

    const getIcon = (channel) => {
        switch(channel) {
            case 'SMS': return <MessageSquare size={16} className="text-emerald-400" />;
            case 'E-Mail': return <Mail size={16} className="text-blue-400" />;
            case 'Push Notification': return <Bell size={16} className="text-amber-400" />;
            default: return <Clock size={16} />;
        }
    };

    return (
        <div className="flex flex-col h-full">
            <div className="flex items-center gap-2 mb-4">
                <History className="text-brand-primary" size={20} />
                <h2 className="text-xl font-bold text-slate-200">Notification History</h2>
            </div>
            
            <div className="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                <table className="w-full text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr className="text-slate-500 text-xs uppercase tracking-widest">
                            <th className="px-4 py-2 font-medium">User</th>
                            <th className="px-4 py-2 font-medium">Category</th>
                            <th className="px-4 py-2 font-medium">Channel</th>
                            <th className="px-4 py-2 font-medium">Message</th>
                            <th className="px-4 py-2 font-medium">Time</th>
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
                                        <div className="flex items-center gap-2 text-sm text-slate-300">
                                            {getIcon(log.channel)}
                                            {log.channel}
                                        </div>
                                    </td>
                                    <td className="px-4 py-4 border-y border-slate-700/50 max-w-xs">
                                        <p className="text-sm text-slate-400 truncate group-hover:whitespace-normal group-hover:overflow-visible group-hover:break-words">
                                            {log.message}
                                        </p>
                                    </td>
                                    <td className="px-4 py-4 rounded-r-lg border-y border-r border-slate-700/50 text-xs text-slate-500 whitespace-nowrap">
                                        {new Date(log.created_at).toLocaleTimeString()}
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
        </div>
    );
};
