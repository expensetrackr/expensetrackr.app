import { defineStepper } from "@stepperize/react";
import { accountSchema } from "#/schemas/account.ts";
import { typeStepSchema } from "./type-step.tsx";

export const detailsSchema = accountSchema.pick({ name: true, description: true });
export const balanceSchema = accountSchema.pick({ initial_balance: true, current_balance: true, currency_code: true });

export const stepperInstance = defineStepper(
    { id: "type", label: "Type", schema: typeStepSchema },
    { id: "details", label: "Details", schema: detailsSchema },
    { id: "balance", label: "Balance & Currency", schema: balanceSchema },
    { id: "complete", label: "Account Summary", schema: typeStepSchema },
);