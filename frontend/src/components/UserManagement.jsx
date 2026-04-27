import React, { useState, useEffect } from 'react';
import { Users, Send, AlertCircle, CheckCircle2, UserPlus } from 'lucide-react';
import { GlassCard } from './GlassCard';
import api from '../lib/api';

const DEFAULT_USER_JSON = `{
  "name": "Jane Doe",
  "email": "jane.doe@example.com",
  "phone": "+1234567890",
  "categories": ["Sports", "Finance"],
  "channels": ["SMS", "E-Mail"]
}`;

export const UserManagement = () => {
    const [jsonPayload, setJsonPayload] = useState(DEFAULT_USER_JSON);
    const [status, setStatus] = useState('idle');
    const [message, setMessage] = useState('');
    const [users, setUsers] = useState([]);

    const fetchUsers = async () => {
        try {
            const res = await api.get('/users');
            setUsers(res.data);
        } catch (err) {
            console.error('Failed to fetch users', err);
        }
    };

    useEffect(() => {
        fetchUsers();
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setStatus('loading');
        setMessage('');

        try {
            const payload = JSON.parse(jsonPayload);
            await api.post('/users', payload);
            setStatus('success');
            setMessage('User created successfully!');
            fetchUsers();
            
            // Reset to default after 3s
            setTimeout(() => {
                setStatus('idle');
                setMessage('');
                setJsonPayload(DEFAULT_USER_JSON);
            }, 3000);
        } catch (err) {
            setStatus('error');
            if (err instanceof SyntaxError) {
                setMessage('Invalid JSON format.');
            } else {
                setMessage(err.response?.data?.message || 'Failed to create user.');
            }
        }
    };

    return (
        <GlassCard className="flex flex-col">
            <div className="flex items-center gap-2 mb-6">
                <Users className="text-purple-400" size={24} />
                <h2 className="text-2xl font-bold text-white tracking-tight">Simple New User Request</h2>
            </div>

            <p className="text-slate-400 text-sm mb-6">
                Use this endpoint simulator to create new users in the system. Ensure the categories and channels exist in the database (e.g. Sports, Finance, Movies, SMS, E-Mail, Push Notification).
            </p>

            <form onSubmit={handleSubmit} className="flex flex-col gap-6">
                <div className="flex flex-col gap-2">
                    <label className="text-xs font-bold text-slate-400 uppercase tracking-widest flex justify-between">
                        JSON Payload
                        <span className="text-purple-400">POST /api/users</span>
                    </label>
                    <textarea
                        value={jsonPayload}
                        onChange={(e) => setJsonPayload(e.target.value)}
                        className="w-full h-[250px] p-4 bg-slate-900/80 border border-slate-700 rounded-xl text-emerald-400 font-mono text-xs focus:outline-none focus:border-purple-500 transition-colors resize-none"
                        placeholder="Enter JSON here..."
                        spellCheck={false}
                    />
                </div>

                <button
                    type="submit"
                    disabled={status === 'loading'}
                    className="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-[0_0_20px_rgba(147,51,234,0.3)] hover:shadow-[0_0_30px_rgba(147,51,234,0.5)] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    {status === 'loading' ? (
                        <div className="animate-spin h-5 w-5 border-2 border-white/20 border-t-white rounded-full" />
                    ) : (
                        <>
                            <Send size={18} />
                            Send Request
                        </>
                    )}
                </button>

                {/* Status Message */}
                {status === 'success' && (
                    <div className="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-start gap-3">
                        <CheckCircle2 size={20} className="shrink-0 mt-0.5" />
                        <p className="text-sm">{message}</p>
                    </div>
                )}
                {status === 'error' && (
                    <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-start gap-3">
                        <AlertCircle size={20} className="shrink-0 mt-0.5" />
                        <p className="text-sm">{message}</p>
                    </div>
                )}
            </form>

            <div className="mt-8 pt-6 border-t border-slate-800">
                <div className="flex items-center gap-2 mb-4">
                    <UserPlus className="text-slate-400" size={16} />
                    <h3 className="text-sm font-bold text-slate-300 uppercase tracking-widest">Current Users ({users.length})</h3>
                </div>
                <div className="flex flex-wrap gap-2">
                    {users.slice(0, 10).map(u => (
                        <span key={u.id} className="text-xs bg-slate-800 text-slate-300 px-2 py-1 rounded-md border border-slate-700">
                            {u.name}
                        </span>
                    ))}
                    {users.length > 10 && (
                        <span className="text-xs bg-slate-800/50 text-slate-500 px-2 py-1 rounded-md border border-slate-800">
                            +{users.length - 10} more
                        </span>
                    )}
                </div>
            </div>
        </GlassCard>
    );
};
