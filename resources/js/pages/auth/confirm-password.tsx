import { Head, useForm } from "@inertiajs/react";
import { useEffect } from "react";

import { Button } from "#/components/button";
import { ErrorMessage, Field, Label } from "#/components/fieldset";
import { Input } from "#/components/input";
import { AuthLayout } from "#/layouts/auth-layout";

export default function ConfirmPassword() {
	const { data, setData, post, processing, errors, reset } = useForm({
		password: "",
	});

	// biome-ignore lint/correctness/useExhaustiveDependencies: not need to be updated on reset change
	useEffect(() => {
		return () => {
			reset("password");
		};
	}, []);

	const submit: React.FormEventHandler = (e) => {
		e.preventDefault();

		post(route("password.confirm"));
	};

	return (
		<AuthLayout description="This is a secure area of the application. Please confirm your password before continuing.">
			<Head title="Confirm Password" />

			<form className="flex flex-col gap-3" onSubmit={submit}>
				<Field>
					<Label>Password</Label>
					<Input
						autoComplete="current-password"
						autoFocus
						invalid={!!errors.password}
						name="password"
						type="password"
						onChange={(e) => setData("password", e.target.value)}
						placeholder="Enter your password"
						value={data.password}
					/>
					{errors.password && <ErrorMessage>{errors.password}</ErrorMessage>}
				</Field>

				<Button type="submit" disabled={processing}>
					Confirm
				</Button>
			</form>
		</AuthLayout>
	);
}