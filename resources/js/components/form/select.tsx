import * as Headless from "@headlessui/react";
import { type VariantProps } from "cva";
import { forwardRef } from "react";

import { cva, cx } from "#/utils/cva.ts";

const selectVariants = cva({
    base: [
        // Basic layout
        "relative block w-full appearance-none ring-0 shadow-none transition",
        // Typography
        "text-paragraph-sm placeholder:text-[var(--text-soft-400)] placeholder:transition-colors focus-visible:placeholder:text-[var(--text-strong-950)] data-hover:placeholder:text-[var(--text-sub-600)]",
        // Border
        "border border-[var(--stroke-soft-200)] focus-visible:border-[var(--stroke-strong-950)] data-hover:border-[var(--bg-weak-50)]",
        // Background color
        "bg-[var(--bg-white-0)] focus-visible:border-[var(--stroke-strong-950)] data-hover:bg-[var(--bg-weak-50)]",
        // Hide default focus styles
        "focus:outline-none",
        // Invalid state
        "data-invalid:border-state-error-base",
        // Disabled state
        "data-disabled:bg-[var(--bg-weak-50)] data-disabled:text-[var(--text-disabled-300)]",
    ],
    variants: {
        $size: {
            xs: "rounded-8 h-8 p-1.5",
            sm: "rounded-8 h-9 p-2",
            md: "rounded-10 h-10 p-2.5",
        },
    },
    defaultVariants: {
        $size: "md",
    },
});

export const Select = forwardRef(function Select(
    {
        className,
        multiple,
        $size = "md",
        ...props
    }: { className?: string } & Omit<Headless.SelectProps, "as" | "className"> & VariantProps<typeof selectVariants>,
    ref: React.ForwardedRef<HTMLSelectElement>,
) {
    return (
        <span
            className={cx(
                className,
                // Basic layout
                "group relative block w-full before:transition after:transition",
                // Background color + shadow applied to inset pseudo-element, so shadow blends with border in light mode
                "before:absolute before:inset-px before:bg-white before:shadow",
                ($size === "xs" || $size === "sm") && "before:rounded-[calc(var(--radius-8)-1px)]",
                $size === "md" && "before:rounded-[calc(var(--radius-10)-1px)]",
                // Background color is moved to control and shadow is removed in dark mode so hide `before` pseudo
                "dark:before:hidden",
                // Focus ring
                "sm:focus-within:after:outline-neutral-alpha-16 after:pointer-events-none after:absolute after:inset-0 after:ring-transparent after:outline after:outline-transparent sm:focus-within:after:outline-2 sm:focus-within:after:outline-offset-2",
                ($size === "xs" || $size === "sm") && "after:rounded-8",
                $size === "md" && "after:rounded-10",
                // Disabled state
                "has-data-disabled:opacity-50 before:has-data-disabled:bg-zinc-950/5 before:has-data-disabled:shadow-none",
            )}
            data-slot="control"
        >
            <Headless.Select
                multiple={multiple}
                ref={ref}
                {...props}
                className={selectVariants({
                    $size,
                })}
            />
            {!multiple && (
                <span className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                    <svg
                        aria-hidden="true"
                        className="size-5 stroke-zinc-500 group-has-data-disabled:stroke-zinc-600 sm:size-4 dark:stroke-zinc-400 forced-colors:stroke-[CanvasText]"
                        fill="none"
                        viewBox="0 0 16 16"
                    >
                        <path
                            d="M5.75 10.75L8 13L10.25 10.75"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={1.5}
                        />
                        <path
                            d="M10.25 5.25L8 3L5.75 5.25"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={1.5}
                        />
                    </svg>
                </span>
            )}
        </span>
    );
});