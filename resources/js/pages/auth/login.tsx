import { Head, useForm } from "@inertiajs/react";
import { useEffect } from "react";
import { toast } from "sonner";

import { Button } from "#/components/button";
import { Checkbox, CheckboxField } from "#/components/checkbox";
import { ErrorMessage, Field, Label } from "#/components/fieldset";
import { Input } from "#/components/input";
import { StyledLink } from "#/components/link";
import { Socialstream } from "#/components/socialstream";
import { AuthLayout } from "#/layouts/auth-layout";
import type { InertiaSharedProps } from "#/types";

export default function Login({
	status,
	canResetPassword,
	socialstream,
}: InertiaSharedProps<{ status?: string; canResetPassword: boolean }>) {
	const { data, setData, post, processing, errors, reset } = useForm({
		email: "",
		password: "",
		remember: false,
	});

	// biome-ignore lint/correctness/useExhaustiveDependencies: not need to be updated on reset change
	useEffect(() => {
		return () => {
			reset("password");
		};
	}, []);

	useEffect(() => {
		if (status) {
			toast.info(status);
		}
	}, [status]);

	const submit: React.FormEventHandler = (e) => {
		e.preventDefault();

		post(route("login"));
	};

	return (
		<AuthLayout
			title="Log in to your account"
			description="Welcome back! Please enter your details"
			footer={
				<>
					Don’t your have account?{" "}
					<StyledLink $color="black" className="font-bold" href={route("register")}>
						Sign up
					</StyledLink>
				</>
			}
		>
			<Head title="Log in" />

			<form className="flex flex-col gap-3" onSubmit={submit}>
				<Field>
					<Label>Email</Label>
					<Input
						autoComplete="username"
						autoFocus
						invalid={!!errors.email}
						name="email"
						type="email"
						onChange={(e) => setData("email", e.target.value)}
						value={data.email}
						placeholder="e.g. john@example.com"
					/>
					{errors.email && <ErrorMessage>{errors.email}</ErrorMessage>}
				</Field>

				<Field>
					<Label>Password</Label>
					<Input
						autoComplete="current-password"
						invalid={!!errors.password}
						name="password"
						type="password"
						onChange={(e) => setData("password", e.target.value)}
						value={data.password}
						placeholder="Enter your password"
					/>
					{errors.password && <ErrorMessage>{errors.password}</ErrorMessage>}
				</Field>

				<div className="flex items-center justify-between gap-3 py-2">
					<CheckboxField>
						<Checkbox name="remember" checked={data.remember} onChange={(checked) => setData("remember", checked)} />
						<Label className="text-paragraph-sm">Remember me</Label>
					</CheckboxField>

					{canResetPassword && <StyledLink href={route("password.request")}>Forgot your password?</StyledLink>}
				</div>

				<Button type="submit" disabled={processing}>
					Log in
				</Button>
			</form>

			{socialstream.show && <Socialstream />}
		</AuthLayout>
	);
}