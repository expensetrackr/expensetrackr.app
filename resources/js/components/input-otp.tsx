import DotIcon from "virtual:icons/lucide/dot";
import { OTPInput, OTPInputContext } from "input-otp";
import * as React from "react";
import { cx } from "#/utils/cva";
import { twc } from "#/utils/twc";

export const InputOTP = React.forwardRef<
	React.ElementRef<typeof OTPInput>,
	React.ComponentPropsWithoutRef<typeof OTPInput>
>(({ className, containerClassName, ...props }, ref) => (
	<OTPInput
		ref={ref}
		containerClassName={cx("flex items-center has-[:disabled]:opacity-50", containerClassName)}
		className={cx("disabled:cursor-not-allowed", className)}
		{...props}
	/>
));
InputOTP.displayName = "InputOTP";

export const InputOTPGroup = twc.div`flex items-center`;
InputOTPGroup.displayName = "InputOTPGroup";

export const InputOTPSlot = React.forwardRef<
	React.ElementRef<"div">,
	React.ComponentPropsWithoutRef<"div"> & {
		index: number;
		invalid?: boolean;
	}
>(({ index, invalid, className, ...props }, ref) => {
	const inputOTPContext = React.useContext(OTPInputContext);
	const slot = inputOTPContext.slots[index];

	return (
		<div
			ref={ref}
			className={cx(
				"relative flex h-16 w-full items-center justify-center border-y border-r bg-[var(--bg-white-0)] text-h5 transition after:outline after:outline-transparent first:rounded-l-10 first:border-l last:rounded-r-10 hover:bg-[var(--bg-weak-50)]",
				slot?.isActive &&
					"z-10 border-[var(--stroke-strong-950)] after:pointer-events-none after:absolute after:inset-0 after:outline-2 after:outline-neutral-alpha-16 after:outline-offset-[3px] after:transition",
				invalid && "border-state-error-base",
				className,
			)}
			{...props}
		>
			{slot?.char}
			{slot?.hasFakeCaret && (
				<div className="pointer-events-none absolute inset-0 flex items-center justify-center">
					<div className="h-8 w-px animate-caret-blink bg-[var(--text-strong-950)] duration-1000" />
				</div>
			)}
		</div>
	);
});
InputOTPSlot.displayName = "InputOTPSlot";

export const InputOTPSeparator = React.forwardRef<React.ElementRef<"div">, React.ComponentPropsWithoutRef<"div">>(
	({ ...props }, ref) => (
		// biome-ignore lint/a11y/useSemanticElements: we need to use a div because of the caret animation
		<div ref={ref} role="separator" tabIndex={0} {...props}>
			<DotIcon />
		</div>
	),
);
InputOTPSeparator.displayName = "InputOTPSeparator";