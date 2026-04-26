import React from 'react';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export const GlassCard = ({ children, className }) => {
    return (
        <div className={twMerge(
            "bg-slate-900/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden",
            className
        )}>
            {children}
        </div>
    );
};
